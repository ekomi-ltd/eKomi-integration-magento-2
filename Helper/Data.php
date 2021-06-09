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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Ekomi\EkomiIntegration\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_SHOP_ID         = 'ekomiintegration/general/shop_id';
    const XML_PATH_SHOP_PASSWORD   = 'ekomiintegration/general/shop_password';
    const XML_PATH_PRODUCT_REVIEWS = 'ekomiintegration/general/product_reviews';
    const XML_PATH_ORDER_STATUS    = 'ekomiintegration/general/order_status';
    const XML_PATH_ACTIVE          = 'ekomiintegration/general/active';
    const XML_PATH_REVIEW_MOD      = 'ekomiintegration/general/review_mod';
    const XML_PATH_IDENTIFIER      = 'ekomiintegration/general/product_identifier';
    const XML_PATH_EXCLUDE_IDS     = 'ekomiintegration/general/exclude_products';
    const XML_PATH_EXPORT_METHOD   = 'ekomiintegration/general/export_method';
    const XML_PATH_TURNAROUND_TIME = 'ekomiintegration/general/turnaround_time';
    const XML_PATH_ACTIVE_PRC      = 'ekomiintegration/prc/show_prc';
    const XML_PATH_WIDGET_TOKEN    = 'ekomiintegration/prc/widget_token';
    const XML_PATH_STORE_NAME      = 'trans_email/ident_support/name';
    const XML_PATH_STORE_EMAIL     = 'trans_email/ident_support/email';
    const XML_PATH_TERMS_CONDITION = 'ekomiintegration/general/terms_and_conditions';

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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
     * @return string ExportMethod
     */
    public function getExportMethod($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_EXPORT_METHOD,
            $storeId
        );
    }

    /**
     * @param integer $storeId
     * @return integer TurnaroundTime
     */
    public function getTurnaroundTime($storeId = false)
    {
        return $this->getConfigValue(
            self::XML_PATH_TURNAROUND_TIME,
            $storeId
        );
    }

    /**
     * @param integer $storeId
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
     * @param integer $storeId
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
     * @param integer $storeId
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

    /**
     * @param null $storeId
     * @return bool|string
     */
    public function isTermsAndConditionsAccepted($storeId = null)
    {
        return $this->getConfigValue(
            self::XML_PATH_TERMS_CONDITION,
            $storeId
        );
    }
}
