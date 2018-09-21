<?php
/**
 * Ekomi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
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

/**
 * Class Validate
 *
 * @package Ekomi\EkomiIntegration\Model
 */
class Validate extends \Magento\Framework\App\Config\Value
{
    const GET_SETTINGS_API_URL   = 'http://api.ekomi.de/v3/getSettings';
    const SMART_CHECK_API_URL    = 'https://srr.ekomi.com/api/v1/shops/setting';
    const ACCESS_DENIED_RESPONSE = 'Access denied';

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
     * @param Context               $context
     * @param Registry              $registry
     * @param ScopeConfigInterface  $config
     * @param TypeListInterface     $cacheTypeList
     * @param RequestInterface      $request
     * @param Curl                  $curl
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
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
    ) {
        $this->request = $request;
        $this->curl    = $curl;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return mixed
     *
     */
    public function execute()
    {
        $this->messageManager->addSuccess('Message from new admin controller.');
        return parent::execute();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function beforeSave()
    {
        $postData = $this->request->getPostValue();
        $postValues = $postData['groups']['general']['fields'];
        $shopId = $postValues['shop_id']['value'];
        $shopPw = $postValues['shop_password']['value'];
        $smartCheck = $postValues['smart_check']['value'];

        $server_output = $this->verifyAccount($shopId, $shopPw);
        if ($server_output == null || $server_output == self::ACCESS_DENIED_RESPONSE) {
            $this->setValue(0);
            $errorMsg = 'Access denied, Invalid Shop ID or Password';
            $phrase = new \Magento\Framework\Phrase($errorMsg);
            throw new \Magento\Framework\Exception\LocalizedException($phrase);
        } else {
            $this->updateSmartCheck($shopId, $shopPw, $smartCheck);
            return parent::beforeSave();
        }
    }

    /**
     * @param $shopId
     * @param $shopPw
     *
     * @return string
     */
    private function verifyAccount($shopId, $shopPw)
    {
        $apiUrl = self::GET_SETTINGS_API_URL . "?auth=" . $shopId . "|" . $shopPw .
            "&version=cust-1.0.0&type=request&charset=iso";

        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->get($apiUrl);
        $server_output = $this->curl->getBody();
        return $server_output;
    }

    /**
     * @param $shopId
     * @param $shopPassword
     * @param $smartCheckOn
     *
     * @return bool|string
     */
    private function updateSmartCheck($shopId, $shopPassword, $smartCheckOn)
    {
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode(['smartcheck_on' => (bool)$smartCheckOn]));
        $this->curl->addHeader('shop-id', $shopId);
        $this->curl->addHeader('interface-password', $shopPassword);
        $this->curl->post(
            self::SMART_CHECK_API_URL,
            json_encode(['smartcheck_on' => $smartCheckOn])
        );
        $server_output = $this->curl->getBody();

        return $server_output;
    }

}
