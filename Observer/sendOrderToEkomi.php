<?php
namespace Ekomi\EkomiIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject as Object;

class sendOrderToEkomi implements ObserverInterface
{
    protected $_logger;
    protected $_helper;
    protected $_apiUrl = 'https://apps.ekomi.com/srr/add-recipient';

    /**
     * sendOrderToEkomi constructor.
     *
     * @param \Ekomi\EkomiIntegration\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface            $logger
     * @param array                               $data
     */
    public function __construct(
        \Ekomi\EkomiIntegration\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_helper = $helper;
        // parent::__construct($data);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();

        $statuses = explode(',', $this->_helper->getOrderStatus());
        if (!$this->_helper->getIsActive() || (is_array($statuses) &&
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
    protected function getOrderData($order, $storeId )
    {
        $scheduleTime = date('d-m-Y H:i:s',strtotime($order->getCreatedAt()));
        $apiMode = $this->getRecipientType($order->getBillingAddress()->getTelephone(), $storeId);
        $senderName = $this->_helper->getStoreName();
        if(strlen($senderName) > 11)
            $senderName = substr($senderName, 0, 11);
        $fields = array(
            'shop_id'           => $this->_helper->getShopId(),
            'password'          => $this->_helper->getShopPw(),
            'recipient_type'    => $apiMode,
            'salutation'        => '',
            'first_name'        => $order->getBillingAddress()->getFirstname(),
            'last_name'         => $order->getBillingAddress()->getLastname(),
            'email'             => $order->getCustomerEmail(),
            'transaction_id'    => $order->getIncrementId() . '27nov2016-2',
            'transaction_time'  => $scheduleTime,
            'telephone'         => $order->getBillingAddress()->getTelephone(),
            'sender_name'       => $senderName,
            'sender_email'      => $this->_helper->getStoreEmail()
        );
        if ($order->getCustomerId()) {
            $fields['client_id'] = $order->getCustomerId();
            $fields['screen_name'] = $this->getCustomerScreenName($order->getCustomerId());
        } else {
            $fields['client_id'] = 'guest_oId_' . $order->getIncrementId();
            $lname =  $order->getBillingAddress()->getLastname();
            $fields['screen_name'] = $order->getBillingAddress()->getFirstname() . $lname[0];
        }
        if ($this->_helper->getProductReview()){
            $fields['has_products'] = 1;
            $productsData = $this->getProductsData($order, $storeId);
            $fields['products_info'] = json_encode($productsData['product_info']);
            $fields['products_other'] = json_encode($productsData['other']);
        }
        $postvars = '';
        $counter = 1;
        foreach($fields as $key=>$value) {
            if($counter > 1)$postvars .="&";
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
    protected function getProductsData($order, $storeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productOther = array();
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
            $products['product_info'][$product->getId()] = urlencode(addslashes($item->getName()));
            $product->setStoreId($storeId);
            $canonicalUrl = $product->getUrlModel()->getUrl($product, array('_ignore_category'=>true));
            $productOther = array(
                'product_ids' => array(
                    'gbase' => utf8_decode($product->getSku())
                ),
                'links' => array(
                    array('rel' => 'canonical', 'type' => 'text/html',
                          'href' => utf8_decode($canonicalUrl))
                )
            );
            if($product->getThumbnail() != 'no_selection') {
                $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
                $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
                $productOther['image_url'] = utf8_decode($imageUrl);

            }
            $products['other'][$item->getId()]['product_other'] = $productOther;
        }

        return $products;
    }

    /**
     * @param $customerId
     * @return string
     */
    protected function getCustomerScreenName($customerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $screenName = $this->appendName($customer->getPrefix(), false);
        $screenName .= $this->appendName($customer->getFirstname(), ($screenName != '') ? true : false);
        $screenName .= $this->appendName($customer->getMiddlename(), ($screenName != '') ? true : false);
        $screenName .= $this->appendName($customer->getLastname(), ($screenName != '') ? true : false);
        $screenName .= $this->appendName($customer->getSuffix(), ($screenName != '') ? true : false);
        return $screenName;
    }

    /**
     * @param $param
     * @param bool $space
     * @return string
     */
    protected function appendName($param, $space = true)
    {
        if ($param != '' && $space === true) {
            return ' ' . $param;
        }
        return $param;
    }

    /**
     * @param $postvars
     * @param $boundary
     */
    protected function sendOrderData($postvars)
    {
        $boundary = md5(time());
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_apiUrl);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('ContentType:multipart/form-data;boundary=' . $boundary));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
            $exec = curl_exec($ch);
            curl_close($ch);
            echo "<pre>";
        } catch (\Exception $e) {
            $this->_logger->addError($e->getMessage());
        }
    }

    /**
     * @param $telephone
     * @param $storeId
     * @return string
     */
    protected  function getRecipientType($telephone, $storeId) {
        $reviewMod = $this->_helper->getReviewMod($storeId);
        $apiMode = 'email';
        switch($reviewMod){
            case 'sms':
                $apiMode = 'sms';
                break;
            case 'email':
                $apiMode = 'email';
                break;
            case 'fallback':
                if($this->_helper->validateE164($telephone))
                    $apiMode = 'sms';
                else
                    $apiMode = 'email';
                break;
        }

        return $apiMode;
    }

}