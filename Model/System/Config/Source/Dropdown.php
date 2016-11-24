<?php

/**
 * Used in creating options for order status config value selection
 *
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Dropdown implements ArrayInterface
{
    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $statusModel = $objectManager->create('Magento\Sales\Model\Order\Status');
        $labels = $statusModel->getResourceCollection()->getData();
        foreach ($labels as $status){
            $ret[] = [
                'value' => $status['status'],
                'label' => $status['label']
            ];
        }
        return $ret;
    }
}