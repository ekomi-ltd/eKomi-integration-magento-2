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
 * Class TermsAndConditions
 * @package Ekomi\EkomiIntegration\Model\System\Config\Source
 */
class TermsAndConditions implements ArrayInterface
{
    /**
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => 'Yes'],
            ['value' => '0', 'label' => 'No'],
        ];
    }
}
