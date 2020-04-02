<?php
/**
 * EkomiIntegration Helper
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;
use Magento\Framework\HTTP\Client\Curl;
use Ekomi\EkomiIntegration\Helper\Data as DataHelper;
use Psr\Log\LoggerInterface;

/**
 * Class OrderData
 *
 * @package Ekomi\EkomiIntegration\Helper
 */
class OrderData extends AbstractHelper
{
    const PD_ORDERS_API_URL = 'https://plugins-dashboard.ekomiapps.de/api/v1/order';

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderData constructor.
     * @param Curl $curl
     * @param Data $dataHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Curl $curl,
        DataHelper $dataHelper,
        LoggerInterface $logger
    )
    {
        $this->curl = $curl;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
    }


    /**
     * Extract and format configuration fields and required order data
     *
     * @param Order $order
     * @param int $storeId
     * @return array
     */
    public function getRequiredFields($order, $storeId)
    {
        return [
            'shop_id' => $this->dataHelper->getShopId($storeId),
            'interface_password' => $this->dataHelper->getShopPw($storeId),
            'order_data' => $this->getOrderData($order, $storeId),
            'mode' => $this->dataHelper->getReviewMod($storeId),
            'product_identifier' => $this->dataHelper->getProductIdentifier($storeId),
            'exclude_products' => $this->dataHelper->getExcludeProducts($storeId),
            'product_reviews' => (int) $this->dataHelper->getProductReview($storeId),
            'plugin_name' => 'magento'
        ];
    }

    /**
     * Extracts required data from Order object
     *
     * @param Order $order
     * @param int $storeId
     * @return array
     */
    protected function getOrderData($order, $storeId)
    {
        $addressData  = $this->getAddressData($order);
        $productsData = $this->getProductsData($order);

        return [
            'transaction_id' => $order->getIncrementId(),
            'transaction_time' => $order->getCreatedAt(),
            'status' => $order->getStatus(),
            'email' => $order->getCustomerEmail(),
            'customer_id' => $order->getCustomerId() ? $order->getCustomerId() : 'guest_' . $order->getIncrementId(),
            'address' => $addressData,
            'products' => $productsData,
            'sender_name'  => $this->dataHelper->getStoreName($storeId),
            'sender_email' => $this->dataHelper->getStoreEmail($storeId)
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
            "country" => $address->getCountryId(),
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
     * @param array $orderData
     * @return null|string
     */
    public function sendOrderData($orderData)
    {
        $response = null;
        $boundary = base_convert(time(), 10, 36);
        try {
            $this->curl->setHeaders(['ContentType:multipart/form-data;boundary=' . $boundary]);
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
            $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($orderData));
            $this->curl->post(self::PD_ORDERS_API_URL, $orderData);
            $response = $this->curl->getBody();
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }

        return $response;
    }
}
