<?php
/**
 * Used in creating options for Product Identifier config value selection
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2018 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Identifier
 *
 * @package Ekomi\EkomiIntegration\Model\System\Config\Source
 */
class Identifier implements ArrayInterface
{
    /**
     * Option getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $mod = [
            ['value' => 'id', 'label' => 'Product ID'],
            ['value' => 'sku', 'label' => 'Product SKU']
        ];
    }
}
