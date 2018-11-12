<?php
/**
 * EkomiIntegration observer for sending orders to eKomi on status change event
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2018 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject as Object;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Event\Observer;
use Ekomi\EkomiIntegration\Helper\Data;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class SendOrderToEkomi
 *
 * @package Ekomi\EkomiIntegration\Observer
 */
class SendOrderToEkomi implements ObserverInterface
{
    const PD_ORDERS_API_URL = 'https://plugins-dashboard.ekomiapps.de/api/v1/order';

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
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();

        $statuses = explode(',', $this->helper->getOrderStatus($storeId));
        if (!$this->helper->getIsActive($storeId) || (is_array($statuses) &&
                !empty($statuses) && !in_array($order->getStatus(), $statuses))) {
            return;
        }

        $orderDataJson = $this->getRequiredFields($order, $storeId);
        if ($orderDataJson != '') {
            $this->sendOrderData($orderDataJson);
        }
    }

    /**
     * Extract and format configuration fields and required order data
     *
     * @param Order $order
     * @param int $storeId
     * @return false|string
     */
    private function getRequiredFields($order, $storeId)
    {
        $data = [
            'shop_id' => $this->helper->getShopId($storeId),
            'interface_password' => $this->helper->getShopPw($storeId),
            'order_data' => $this->getOrderData($order, $storeId),
            'mode' => $this->helper->getReviewMod($storeId),
            'product_identifier' => $this->helper->getProductIdentifier($storeId),
            'exclude_products' => $this->helper->getExcludeProducts($storeId),
            'product_reviews' => (int) $this->helper->getProductReview($storeId),
            'plugin_name' => 'magento'
        ];

        return json_encode($data);
    }

    /**
     * Extracts required data from Order object
     *
     * @param Order $order
     * @param int $storeId
     * @return array
     */
    private function getOrderData($order, $storeId)
    {
        $addressData  = $this->getAddressData($order);
        $productsData = $this->getProductsData($order);

        return [
            'transaction_id' => $order->getIncrementId(),
            'transaction_time' => $order->getCreatedAt(),
            'last_updated' => $order->getUpdatedAt(),
            'status' => $order->getStatus(),
            'store_id' => $order->getStoreId(),
            'email' => $order->getCustomerEmail(),
            'customer_id' => $order->getCustomerId() ? $order->getCustomerId() : 'guest_' . $order->getIncrementId(),
            'address' => $addressData,
            'products' => $productsData,
            'sender_name'  => $this->helper->getStoreName($storeId),
            'sender_email' => $this->helper->getStoreEmail($storeId)
        ];
    }

    /**
     * Extracts Required address fields from Order object
     *
     * @param Order $order
     * @return array
     */
    protected function getAddressData($order)
    {
        $address = $order->getShippingAddress();
        if (!$address) {
            $address = $order->getBillingAddress();
        }

        return [
            'first_name' => $address->getFirstname(),
            'last_name' => $address->getLastname(),
            "telephone" => $address->getTelephone(),
            "region" => $address->getRegion(),
            "postcode" => $address->getPostcode(),
            "street" => $address->getStreet()[0],
            "city" => $address->getCity(),
            "country" => $address->getCountryId(),
            "address_type" => $address->getAddressType(),
        ];
    }

    /**
     * Extracts Required product fields from order object
     *
     * @param Order $order
     * @return array
     */
    protected function getProductsData($order)
    {
        $products = [];
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            $product = $item->getProduct();

            $image_url = '';
            if ($product->getThumbnail() != 'no_selection') {
                $store = $order->getStore();
                $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $imageUrl = $baseUrl . 'catalog/product' . $product->getImage();
                $image_url = utf8_decode($imageUrl);
            }
            $canonicalUrl = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);

            $products[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'type' => $product->getTypeId(),
                'name' => $product->getName(),
                'description' => htmlspecialchars($product->getDescription()),
                'price' => $product->getPrice(),
                'url' => $product->getUrl(),
                'image_url' => $image_url,
                'canonicalUrl' => $canonicalUrl,
            ];
        }

        return $products;
    }


    /**
     * Exports formatted Json to Plugins Dashboard
     *
     * @param string $dataJson
     * @return null|string
     */
    private function sendOrderData($dataJson)
    {
        $response = null;
        $boundary = base_convert(time(), 10, 36);
        try {
            $this->curl->setHeaders(['ContentType:multipart/form-data;boundary=' . $boundary]);
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
            $this->curl->setOption(CURLOPT_POSTFIELDS, $dataJson);
            $this->curl->post(self::PD_ORDERS_API_URL, $dataJson);
            $response = $this->curl->getBody();
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }

        return $response;
    }
}
