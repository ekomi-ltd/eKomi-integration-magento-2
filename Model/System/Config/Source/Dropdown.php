<?php
/**
 * Used in creating options for order status config value selection
 *
 */

namespace Ekomi\EkomiIntegration\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Class Dropdown
 *
 * @package Ekomi\EkomiIntegration\Model\System\Config\Source
 */
class Dropdown implements ArrayInterface
{
    /**
     * @var Status
     */
    private $statusCollectionFactory;

    /**
     * Dropdown constructor.
     *
     * @param CollectionFactory $statusCollectionFactory
     */
    public function __construct(CollectionFactory $statusCollectionFactory)
    {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $labels = $this->statusCollectionFactory->create()->toOptionArray();
        foreach ($labels as $status) {
            $statuses[] = [
                'value' => $status['value'],
                'label' => $status['label']
            ];
        }

        return $statuses;
    }
}
