<?php
/**
 * Used in creating options for order status config value selection
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /**
     * Option getter
     *
     * @return array OrderStatuses
     */
    public function toOptionArray()
    {
        $statuses = [];
        $labels = $this->statusCollectionFactory->create()->toOptionArray();
        foreach ($labels as $status) {
            $statuses[] = [
                'value' => $status['value'],
                'label' => __($status['label'])
            ];
        }

        return $statuses;
    }
}