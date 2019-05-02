<?php
/**
 * Used in creating options for Product Identifier config value selection
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TurnaroundTime
 *
 * @package Ekomi\EkomiIntegration\Model\System\Config\Source
 */
class TurnaroundTime implements ArrayInterface
{
    /**
     * Option getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $mod = [
            ['value' => '1', 'label' => '1 Day'],
            ['value' => '2', 'label' => '2 Days'],
            ['value' => '3', 'label' => '3 Days'],
            ['value' => '4', 'label' => '4 Days'],
            ['value' => '5', 'label' => '5 Days'],
            ['value' => '6', 'label' => '6 Days'],
            ['value' => '7', 'label' => '7 Days'],
            ['value' => '8', 'label' => '8 Days'],
            ['value' => '9', 'label' => '9 Days'],
            ['value' => '10', 'label' => '10 Days'],
            ['value' => '15', 'label' => '15 Days'],
            ['value' => '20', 'label' => '20 Days'],
            ['value' => '30', 'label' => '30 Days']
        ];
    }
}
