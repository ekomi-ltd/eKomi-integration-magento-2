<?php
/**
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Block\Widget;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Ekomi\EkomiIntegration\Helper\Data;

/**
 * Class Reviews
 *
 * @package Ekomi\EkomiIntegration\Block\Widget
 */
class Reviews extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const PRODUCT_IDENTIFIER_SKU = 'sku';
    const IDENTIFIER_IDS = 'productIds';
    const IDENTIFIER_SKU = 'productSku';
    const PRODUCT_TYPE_BUNDLE = 'bundle';
    const PRODUCT_TYPE_GROUPED = 'grouped';
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Reviews constructor.
     *
     * @param Registry $registry
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_registry     = $registry;
        $this->_helper       = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get Product ID|SKU
     *
     * @return string
     */
    public function getCurrentProductId()
    {
        $productsData = $this->getProductsDataByType($this->_registry->registry('product'));
        $productIdentifier = $this->_helper->getProductIdentifier($this->getStoreId());

        if ($productIdentifier == self::PRODUCT_IDENTIFIER_SKU) {
            return  implode(',', $productsData[self::IDENTIFIER_SKU]);
        }

        return implode(',', $productsData[self::IDENTIFIER_IDS]);
    }

    /**
     * @param $product
     * @return array
     */
    public function getProductsDataByType($product)
    {
        $productsData = [];
        $associatedProducts = [];
        $productsData[self::IDENTIFIER_IDS][] = $product->getId();
        $productsData[self::IDENTIFIER_SKU][] = $product->getSku();

        $productType = $product->getTypeId();
        if ($productType == self::PRODUCT_TYPE_GROUPED) {
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
        } elseif ($productType == self::PRODUCT_TYPE_BUNDLE) {
            $associatedProducts = $product->getTypeInstance(true)->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product), $product
            );
        } elseif ($productType == self::PRODUCT_TYPE_CONFIGURABLE) {
            $associatedProducts = $product->getTypeInstance(true)->getUsedProducts($product);
        }

        foreach ($associatedProducts as $associatedProduct) {
            $productsData[self::IDENTIFIER_IDS][] = $associatedProduct->getId();
            $productsData[self::IDENTIFIER_SKU][] = $associatedProduct->getSku();
        }

        return $productsData;
    }

    /**
     * Get Store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Gets Shop Id
     *
     * @return string
     */
    public function getShopId()
    {
        return $this->_helper->getShopId($this->getStoreId());
    }

    /**
     * Get Widget Token
     *
     * @return string
     */
    public function getWidgetToken()
    {
        return $this->_helper->getWidgetToken($this->getStoreId());
    }

    /**
     * Checks if Module is enabled
     *
     * @return boolean
     */
    public function isModuleEnabled()
    {
        return $this->_helper->getIsActive($this->getStoreId());
    }

    /**
     * Checks if PRC is enabled
     *
     * @return boolean
     */
    public function isPrcEnabled()
    {
        return $this->_helper->getActivePrc($this->getStoreId());
    }
}
