<?php
/**
 * EkomiIntegration observer for sending orders to eKomi on status change event
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Ekomi\EkomiIntegration\Helper\Data as DataHelper;
use Ekomi\EkomiIntegration\Helper\OrderData;

/**
 * Class SendOrderToEkomi
 *
 * @package Ekomi\EkomiIntegration\Observer
 */
class SendOrderToEkomi implements ObserverInterface
{
    const EXPORT_METHOD_STATUS = 'status';

    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var OrderData
     */
    private $orderDataHelper;


    /**
     * SendOrderToEkomi constructor.
     * @param DataHelper $dataHelper
     * @param OrderData $orderDataHelper
     */
    public function __construct(
        DataHelper $dataHelper,
        OrderData $orderDataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->orderDataHelper = $orderDataHelper;
    }

    /**
     * @param Observer $observer
     * @return string|bool
     */
    public function execute(Observer $observer)
    {
        $response = false;
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();
        $statuses = explode(',', $this->dataHelper->getOrderStatus($storeId));

        if (!$this->dataHelper->getIsActive($storeId) ||
            $this->dataHelper->getExportMethod($storeId) !== self::EXPORT_METHOD_STATUS ||
            (is_array($statuses) && !empty($statuses) && !in_array($order->getStatus(), $statuses))
        ) {
            return $response;
        }

        $orderData = $this->orderDataHelper->getRequiredFields($order, $storeId);
        if ($orderData != '') {
            $response = $this->orderDataHelper->sendOrderData($orderData);
        }

        return $response;
    }
}
