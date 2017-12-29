<?php

namespace Ekomi\EkomiIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package Gielberkers\Example\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    const XML_PATH_SHOP_ID          = 'integration/general/shop_id';
    const XML_PATH_SHOP_PASSWORD    = 'integration/general/shop_password';
    const XML_PATH_PRODUCT_REVIEWS  = 'integration/general/product_reviews';
    const XML_PATH_ORDER_STATUS     = 'integration/general/order_status';
    const XML_PATH_ACTIVE           = 'integration/general/active';
    const XML_PATH_STORE_NAME       = 'trans_email/ident_support/name';
    const XML_PATH_STORE_EMAIL      = 'trans_email/ident_support/email';
    const XML_PATH_REVIEW_MOD       = 'integration/general/review_mod';

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getShopId($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $shopId = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_ID,
                $storeScope,
                $storeId
            );
        } else {
            $shopId = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_ID, $storeScope
            );
        }

        return $shopId;
    }

    /**
     * @return mixed
     */
    public function getShopPw($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $shopPw = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_PASSWORD,
                $storeScope,
                $storeId
            );
        } else {
            $shopPw = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_PASSWORD, $storeScope
            );
        }

        return $shopPw;
    }

    /**
     * @return mixed
     */
    public function getProductReview($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $productReviews = $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_REVIEWS,
                $storeScope,
                $storeId
            );
        } else {
            $productReviews = $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_REVIEWS, $storeScope
            );
        }

        return $productReviews;
    }

    /**
     * @return mixed
     */
    public function getOrderStatus($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $statuses = $this->scopeConfig->getValue(
                self::XML_PATH_ORDER_STATUS,
                $storeScope,
                $storeId
            );
        } else {
            $statuses = $this->scopeConfig->getValue(
                self::XML_PATH_ORDER_STATUS, $storeScope
            );
        }

        return $statuses;
    }

    public function getIsActive($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $isActive = $this->scopeConfig->getValue(
                self::XML_PATH_ACTIVE,
                $storeScope,
                $storeId
            );
        } else {
            $isActive = $this->scopeConfig->getValue(
                self::XML_PATH_ACTIVE, $storeScope
            );
        }

        return $isActive;
    }

    /**
     * @return mixed
     */
    public function getStoreName($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $storeName = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_NAME,
                $storeScope,
                $storeId
            );
        } else {
            $storeName = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_NAME, $storeScope
            );
        }

        return $storeName;
    }

    /**
     * @return mixed
     */
    public function getStoreEmail($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $storeEmail = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_EMAIL,
                $storeScope,
                $storeId
            );
        } else {
            $storeEmail = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_EMAIL, $storeScope
            );
        }

        return $storeEmail;
    }

    public function getReviewMod($storeId = false)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if($storeId){
            $reviewMode = $this->scopeConfig->getValue(
                self::XML_PATH_REVIEW_MOD,
                $storeScope,
                $storeId
            );
        } else {
            $reviewMode = $this->scopeConfig->getValue(
                self::XML_PATH_REVIEW_MOD, $storeScope
            );
        }

        return $reviewMode;
    }

    /**
     * Validates E164 numbers
     * @param $phone
     *
     * @return bool
     */
    function validateE164($phone)
    {
        $pattern = '/^\+?[1-9]\d{1,14}$/';
        preg_match($pattern, $phone, $matches);
        if (!empty($matches)) {
            return true;
        }
        return false;
    }

}