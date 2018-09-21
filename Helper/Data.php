<?php
/**
 * EkomiIntegration Helper
 *
 * @category    Ekomi
 * @package     Ekomi_EkomiIntegration
 * @author      Ekomi Private Limited
 *
 */

namespace Ekomi\EkomiIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Ekomi\EkomiIntegration\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
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
    const XML_PATH_SMART_CHECK      = 'integration/general/smart_check';

    /**
     * Data constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getShopId($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $shopId = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_ID,
                $storeScope,
                $storeId
            );
        } else {
            $shopId = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_ID,
                $storeScope
            );
        }

        return $shopId;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getShopPw($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $shopPw = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_PASSWORD,
                $storeScope,
                $storeId
            );
        } else {
            $shopPw = $this->scopeConfig->getValue(
                self::XML_PATH_SHOP_PASSWORD,
                $storeScope
            );
        }

        return $shopPw;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getProductReview($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $productReviews = $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_REVIEWS,
                $storeScope,
                $storeId
            );
        } else {
            $productReviews = $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_REVIEWS,
                $storeScope
            );
        }

        return $productReviews;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getOrderStatus($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $statuses = $this->scopeConfig->getValue(
                self::XML_PATH_ORDER_STATUS,
                $storeScope,
                $storeId
            );
        } else {
            $statuses = $this->scopeConfig->getValue(
                self::XML_PATH_ORDER_STATUS,
                $storeScope
            );
        }

        return $statuses;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getIsActive($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $isActive = $this->scopeConfig->getValue(
                self::XML_PATH_ACTIVE,
                $storeScope,
                $storeId
            );
        } else {
            $isActive = $this->scopeConfig->getValue(
                self::XML_PATH_ACTIVE,
                $storeScope
            );
        }

        return $isActive;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getStoreName($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $storeName = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_NAME,
                $storeScope,
                $storeId
            );
        } else {
            $storeName = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_NAME,
                $storeScope
            );
        }

        return $storeName;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getStoreEmail($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $storeEmail = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_EMAIL,
                $storeScope,
                $storeId
            );
        } else {
            $storeEmail = $this->scopeConfig->getValue(
                self::XML_PATH_STORE_EMAIL,
                $storeScope
            );
        }

        return $storeEmail;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getReviewMod($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $reviewMode = $this->scopeConfig->getValue(
                self::XML_PATH_REVIEW_MOD,
                $storeScope,
                $storeId
            );
        } else {
            $reviewMode = $this->scopeConfig->getValue(
                self::XML_PATH_REVIEW_MOD,
                $storeScope
            );
        }

        return $reviewMode;
    }

    /**
     * @param bool $storeId
     *
     * @return mixed
     */
    public function getSmartCheck($storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $smartCheck = $this->scopeConfig->getValue(
                self::XML_PATH_SMART_CHECK,
                $storeScope,
                $storeId
            );
        } else {
            $smartCheck = $this->scopeConfig->getValue(
                self::XML_PATH_SMART_CHECK,
                $storeScope
            );
        }

        return $smartCheck;
    }

    /**
     * Validates E164 numbers
     * @param $phone
     *
     * @return bool
     */
    public function validateE164($phone)
    {
        $pattern = '/^\+?[1-9]\d{1,14}$/';
        preg_match($pattern, $phone, $matches);
        if (!empty($matches)) {
            return true;
        }

        return false;
    }

}
