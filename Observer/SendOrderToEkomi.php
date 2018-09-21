<?php
/**
 * EkomiIntegration observer for sending orders to eKomi on status change event
 *
 * @category    Ekomi
 * @package     Ekomi_EkomiIntegration
 * @author      Ekomi Private Limited
 *
 */

namespace Ekomi\EkomiIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject as Object;
use Magento\Framework\HTTP\Client\Curl;
use Ekomi\EkomiIntegration\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class SendOrderToEkomi
 *
 * @package Ekomi\EkomiIntegration\Observer
 */
class SendOrderToEkomi implements ObserverInterface
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @var \Ekomi\EkomiIntegration\Helper\Data
     */
    private $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $apiUrl = 'https://srr.ekomi.com/add-recipient';

    /**
     * SendOrderToEkomi constructor.
     *
     * @param Curl            $curl
     * @param Data            $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Curl $curl,
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();

        $statuses = explode(',', $this->helper->getOrderStatus($storeId));
        if (!$this->helper->getIsActive($storeId) || (is_array($statuses) &&
                !empty($statuses) && !in_array($order->getStatus(), $statuses))) {
            return;
        }

        $postvars = $this->getOrderData($order, $storeId);
        if ($postvars != '') {
            $this->sendOrderData($postvars);
        }
    }

    /**
     * @param $order
     * @param $storeId
     *
     * @return string
     */
    private function getOrderData($order, $storeId)
    {
        $scheduleTime = date('d-m-Y H:i:s', strtotime($order->getCreatedAt()));
        $apiMode = $this->getRecipientType($order->getBillingAddress()->getTelephone(), $storeId);
        $senderName = $this->helper->getStoreName($storeId);
        $firstName = $order->getBillingAddress()->getFirstname();
        $lastName  = $order->getBillingAddress()->getLastname();
        if (strlen($senderName) > 11) {
            $senderName = substr($senderName, 0, 11);
        }
        $fields = [
            'shop_id'           => $this->helper->getShopId($storeId),
            'password'          => $this->helper->getShopPw($storeId),
            'recipient_type'    => $apiMode,
            'salutation'        => '',
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'email'             => $order->getCustomerEmail(),
            'transaction_id'    => $order->getIncrementId(),
            'transaction_time'  => $scheduleTime,
            'telephone'         => $order->getBillingAddress()->getTelephone(),
            'sender_name'       => $senderName,
            'sender_email'      => $this->helper->getStoreEmail($storeId)
        ];
        if ($order->getCustomerId()) {
            $fields['client_id'] = $order->getCustomerId();
            $fields['screen_name'] = $firstName . ' ' . $lastName;
        } else {
            $fields['client_id'] = 'guest_oId_' . $order->getIncrementId();
            $fields['screen_name'] = $firstName . ' ' . $lastName;
        }
        if (!$this->helper->getProductReview($storeId)) {
            $fields['has_products'] = 1;
            $productsData = $this->getProductsData($order, $storeId);
            $fields['products_info'] = json_encode($productsData['product_info']);
            $fields['products_other'] = json_encode($productsData['other']);
        }

        $postvars = '';
        $counter = 1;
        foreach ($fields as $key => $value) {
            if ($counter > 1) {
                $postvars .="&";
            }
            $postvars .= $key . "=" . $value;
            $counter++;
        }

        return $postvars;
    }

    /**
     * @param $order
     * @param $storeId
     *
     * @return mixed
     */
    private function getProductsData($order, $storeId)
    {
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            $product = $item->getProduct();
            $products['product_info'][$product->getId()] = urlencode(addslashes($item->getName()));
            $product->setStoreId($storeId);
            $canonicalUrl = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
            $productOther = [
                'product_ids' => [
                    'gbase' => utf8_decode($product->getId())
                ],
                'links' => [
                    [
                        'rel' => 'canonical',
                        'type' => 'text/html',
                        'href' => utf8_decode($canonicalUrl)
                    ]
                ]
            ];
            if ($product->getThumbnail() != 'no_selection') {
                $store = $order->getStore();
                $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $imageUrl = $baseUrl . 'catalog/product' . $product->getImage();
                $productOther['image_url'] = utf8_decode($imageUrl);
            }
            $products['other'][$item->getId()]['product_other'] = $productOther;
        }

        return $products;
    }

    /**
     * @param $postvars
     *
     * @return null|string
     */
    private function sendOrderData($postvars)
    {
        $response = null;
        $boundary = base_convert(time(), 10, 36);
        try {
            $this->curl->setHeaders(['ContentType:multipart/form-data;boundary=' . $boundary]);
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->post($this->apiUrl, $postvars);
            $response = $this->curl->getBody();
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }

        return $response;
    }

    /**
     * @param $telephone
     * @param $storeId
     *
     * @return string
     */
    private function getRecipientType($telephone, $storeId)
    {
        $reviewMod = $this->helper->getReviewMod($storeId);
        $apiMode = 'email';
        switch ($reviewMod) {
            case 'sms':
                $apiMode = 'sms';
                break;
            case 'email':
                $apiMode = 'email';
                break;
            case 'fallback':
                if ($this->helper->validateE164($telephone)) {
                    $apiMode = 'sms';
                } else {
                    $apiMode = 'email';
                }
                break;
        }

        return $apiMode;
    }

}
