<?php
/**
 * Ekomi
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Phrase;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Validate
 *
 * @package Ekomi\EkomiIntegration\Model
 */
class Validate extends \Magento\Framework\App\Config\Value
{
    const GET_SETTINGS_API_URL = 'http://api.ekomi.de/v3/getSettings';
    const SMART_CHECK_API_URL  = 'https://srr.ekomi.com/api/v1/shops/setting';
    const CUSTOMER_SEGMENT_URL = 'https://srr.ekomi.com/api/v1/customer-segments';
    const ACCESS_DENIED_RESPONSE = 'Access denied';
    const SEGMENT_STATUS_ACTIVE = "active";
    const SEGMENT_STATUS_INACTIVE = "inactive";
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_STATUS_OK = 200;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * Validate constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param RequestInterface $request
     * @param Curl $curl
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        RequestInterface $request,
        Curl $curl,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->request = $request;
        $this->curl = $curl;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Triggers on saving configuration form
     *
     * @return \Magento\Framework\App\Config\Value
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $postData = $this->request->getPostValue();
        $postValues = $postData['groups']['general']['fields'];
        $shopId = $postValues['shop_id']['value'];
        $shopPassword = $postValues['shop_password']['value'];
        $smartCheck = $postValues['smart_check']['value'];

        $server_output = $this->verifyAccount($shopId, $shopPassword);
        if ($server_output == null || $server_output == self::ACCESS_DENIED_RESPONSE) {
            $this->setValue(0);
            $errorMsg = 'Access denied, Invalid Shop ID or Password';
            $phrase = new Phrase($errorMsg);
            throw new LocalizedException($phrase);
        } else {
            $customerSegment = $this->getSrrCustomerSegment($shopId, $shopPassword);
            if ($customerSegment !== false && is_array($customerSegment)) {
                $this->activateSrrCustomerSegment($shopId, $shopPassword, $customerSegment["id"]);
            }

            $this->updateSmartCheck($shopId, $shopPassword, $smartCheck);

            return parent::beforeSave();
        }
    }

    /**
     * Validates eKomi account credentials
     *
     * @param string $shopId
     * @param string $shopPassword
     * @return string
     */
    private function verifyAccount($shopId, $shopPassword)
    {
        $apiUrl = self::GET_SETTINGS_API_URL . "?auth=" . $shopId . "|" . $shopPassword .
            "&version=cust-1.0.0&type=request&charset=iso";

        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->get($apiUrl);
        $server_output = $this->curl->getBody();

        return $server_output;
    }

    /**
     * @param string $shopId
     * @param string $shopPassword
     * @return bool|array
     */
    public function getSrrCustomerSegment($shopId, $shopPassword)
    {
        $apiUrl = self::CUSTOMER_SEGMENT_URL . '?status=' . self::SEGMENT_STATUS_INACTIVE;

        $this->configureCurl($shopId, $shopPassword, self::HTTP_METHOD_GET);
        $this->curl->get($apiUrl);
        $responseJson = $this->curl->getBody();

        $response = json_decode($responseJson, true);
        if ($response['status_code'] !== self::HTTP_STATUS_OK) {
            return false;
        }

        $customerSegments = $response['data'];
        $defaultSegmentKey = $this->getDefaultSegmentKey($customerSegments);
        if ($defaultSegmentKey === false) {
            return false;
        }

        return $customerSegments[$defaultSegmentKey];
    }

    /**
     * @param array $customerSegments
     * @return bool|int|string
     */
    public function getDefaultSegmentKey($customerSegments)
    {
        foreach($customerSegments as $key => $customerSegment)
        {
            if ( $customerSegment['is_default'] == 'true' )
                return $key;
        }

        return false;
    }

    /**
     * @param string $shopId
     * @param string $shopPassword
     * @param int $segmentId
     * @return string
     */
    public function activateSrrCustomerSegment($shopId, $shopPassword, $segmentId)
    {
        $apiUrl = self::CUSTOMER_SEGMENT_URL . '/' . $segmentId . '?status=' . self::SEGMENT_STATUS_ACTIVE;
        $this->configureCurl($shopId, $shopPassword, self::HTTP_METHOD_PUT);
        $this->curl->post(
            $apiUrl,
            []
        );
        $responseJson = $this->curl->getBody();

        return $responseJson;
    }

    /**
     * Updates Smart Check value on SRR
     *
     * @param string $shopId
     * @param string $shopPassword
     * @param boolean $smartCheckOn
     * @return bool|string
     */
    private function updateSmartCheck($shopId, $shopPassword, $smartCheckOn)
    {
        $this->configureCurl(
            $shopId,
            $shopPassword,
            self::HTTP_METHOD_PUT,
            ['smartcheck_on' => (bool)$smartCheckOn]
        );
        $this->curl->post(
            self::SMART_CHECK_API_URL,
            ['smartcheck_on' => $smartCheckOn]
        );
        $response = $this->curl->getBody();

        return $response;
    }

    /**
     * @param string $shopId
     * @param string $shopPassword
     * @param string $httpMethod
     * @param array $postFields
     */
    private function configureCurl($shopId, $shopPassword, $httpMethod = self::HTTP_METHOD_GET, $postFields = null)
    {
        $this->curl->addHeader('shop-id', $shopId);
        $this->curl->addHeader('interface-password', $shopPassword);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        if ($httpMethod === self::HTTP_METHOD_PUT) {
            $this->curl->setOption(CURLOPT_CUSTOMREQUEST, self::HTTP_METHOD_PUT);
        }
        if ($postFields !== null && is_array($postFields)) {
            $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($postFields));
        }
    }

}