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
            ['value' => '1', 'label' => __('1 Day')],
            ['value' => '2', 'label' => __('2 Days')],
            ['value' => '3', 'label' => __('3 Days')],
            ['value' => '4', 'label' => __('4 Days')],
            ['value' => '5', 'label' => __('5 Days')],
            ['value' => '6', 'label' => __('6 Days')],
            ['value' => '7', 'label' => __('7 Days')],
            ['value' => '8', 'label' => __('8 Days')],
            ['value' => '9', 'label' => __('9 Days')],
            ['value' => '10', 'label' => __('10 Days')],
            ['value' => '15', 'label' => __('15 Days')],
            ['value' => '20', 'label' => __('20 Days')],
            ['value' => '30', 'label' => __('30 Days')]
        ];
    }
}
