<?php

/**
 * Used in creating options for order status config value selection
 *
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Mod implements ArrayInterface
{
    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        return $mod = [['value' => 'email', 'label' => 'Email'],
            ['value' => 'sms', 'label' => 'SMS'],
            ['value' => 'fallback', 'label' => 'SMS if contact number valid, otherwise Email']];
    }
}