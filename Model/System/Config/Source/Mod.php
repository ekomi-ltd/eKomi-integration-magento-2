<?php
/**
 * Used in creating options for Mode config value selection
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Mod
 *
 * @package Ekomi\EkomiIntegration\Model\System\Config\Source
 */
class Mod implements ArrayInterface
{
    /**
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        return $mod = [
            ['value' => 'email', 'label' => __('Email')],
            ['value' => 'sms', 'label' => __('SMS')],
            ['value' => 'fallback', 'label' => __('SMS if contact number valid, otherwise Email')]
        ];
    }
}
