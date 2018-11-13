<?php
/**
 * EkomiIntegration Helper
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2018 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    const XML_PATH_SHOP_ID         = 'integration/general/shop_id';
    const XML_PATH_SHOP_PASSWORD   = 'integration/general/shop_password';
    const XML_PATH_PRODUCT_REVIEWS = 'integration/general/product_reviews';
    const XML_PATH_ORDER_STATUS    = 'integration/general/order_status';
    const XML_PATH_ACTIVE          = 'integration/general/active';
    const XML_PATH_REVIEW_MOD      = 'integration/general/review_mod';
    const XML_PATH_IDENTIFIER      = 'integration/general/product_identifier';
    const XML_PATH_EXCLUDE_IDS     = 'integration/general/exclude_products';
    const XML_PATH_SMART_CHECK     = 'integration/general/smart_check';
    const XML_PATH_ACTIVE_PRC      = 'integration/prc/show_prc';
    const XML_PATH_WIDGET_TOKEN    = 'integration/prc/widget_token';
    const XML_PATH_STORE_NAME      = 'trans_email/ident_support/name';
    const XML_PATH_STORE_EMAIL     = 'trans_email/ident_support/email';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
     * @return string ShopId
     */
    public function getShopId($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_SHOP_ID,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string ShopPassword
     */
    public function getShopPw($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_SHOP_PASSWORD,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return bool ProductReviews
     */
    public function getProductReview($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_PRODUCT_REVIEWS,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string OrderStatuses (comma separated)
     */
    public function getOrderStatus($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_ORDER_STATUS,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return bool Active
     */
    public function getIsActive($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_ACTIVE,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string StoreName
     */
    public function getStoreName($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_STORE_NAME,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string StoreEmail
     */
    public function getStoreEmail($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_STORE_EMAIL,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string ReviewMod (email, sms, fallback)
     */
    public function getReviewMod($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_REVIEW_MOD,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string ProductIdentifier (id, sku)
     */
    public function getProductIdentifier($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_IDENTIFIER,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string ExcludeProducts (comma separated ids)
     */
    public function getExcludeProducts($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_EXCLUDE_IDS,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return bool SmartCheck
     */
    public function getSmartCheck($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_SMART_CHECK,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return bool ActivePrc
     */
    public function getActivePrc($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_ACTIVE_PRC,
            $storeId
        );
    }

    /**
     * @param bool $storeId
     * @return string WidgetToken
     */
    public function getWidgetToken($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_WIDGET_TOKEN,
            $storeId
        );
    }

    /**
     * @param string $configPath
     * @param bool $storeId
     * @return bool|string
     */
    private function getConfigValue($configPath, $storeId = false)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        if ($storeId) {
            $value = $this->scopeConfig->getValue(
                $configPath,
                $storeScope,
                $storeId
            );
        } else {
            $value = $this->scopeConfig->getValue(
                $configPath,
                $storeScope
            );
        }

        return $value;
    }
}
