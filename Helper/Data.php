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
    public function getShopId()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOP_ID, $storeScope
        );
    }

    /**
     * @return mixed
     */
    public function getShopPw()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOP_PASSWORD, $storeScope
        );
    }

    /**
     * @return mixed
     */
    public function getProductReview()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_REVIEWS, $storeScope
        );
    }

    /**
     * @return mixed
     */
    public function getOrderStatus()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_STATUS, $storeScope
        );
    }

    public function getIsActive()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACTIVE, $storeScope
        );
    }

    /**
     * @return mixed
     */
    public function getStoreName()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_STORE_NAME, $storeScope
        );
    }

    /**
     * @return mixed
     */
    public function getStoreEmail()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_STORE_EMAIL, $storeScope
        );
    }

    public function getReviewMod()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_REVIEW_MOD, $storeScope
        );
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