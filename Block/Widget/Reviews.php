<?php
/**
 * @category    Ekomi
 * @copyright   Copyright (c) 2018 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Block\Widget;

use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
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

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

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
        StoreManagerInterface $storeManager,
        Registry $registry,
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
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
        $productIdentifier = $this->_helper->getProductIdentifier();
        if ($productIdentifier == self::PRODUCT_IDENTIFIER_SKU) {
            return  $this->_registry->registry('product')->getSku();
        }

        return $this->_registry->registry('product')->getId();
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
