<?php
/**
 * EkomiIntegration Helper
 *
 * @category    Ekomi
 * @copyright   Copyright (c) 2019 Ekomi ltd (http://www.ekomi.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ekomi\EkomiIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Ekomi\EkomiIntegration\Helper\Data as DataHelper;
use Ekomi\EkomiIntegration\Helper\OrderData;
use Magento\Store\Model\StoreRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;

    /**
 * Class Data
 *
 * @package Ekomi\EkomiIntegration\Helper
 */
class Export extends AbstractHelper
{
    const EXPORT_METHOD_CRON = 'cron';
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var OrderData
     */
    private $orderDatahelper;

    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Export constructor.
     * @param Data $dataHelper
     * @param OrderData $orderDatahelper
     * @param StoreRepository $storeRepository
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DateTime $date
     */
    public function __construct(
        DataHelper $dataHelper,
        OrderData $orderDatahelper,
        StoreRepository $storeRepository,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $date
    )
    {
        $this->dataHelper = $dataHelper;
        $this->orderDatahelper = $orderDatahelper;
        $this->storeRepository = $storeRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->date = $date;
    }

    /**
     * Export Orders to eKomi
     * @return array
     */
    public function exportOrders()
    {
        $response = [];
        $stores = $this->getStores();
        foreach ($stores as $storeId => $storeName) {
            if (!$this->dataHelper->getIsActive($storeId) ||
                $this->dataHelper->getExportMethod($storeId) !== self::EXPORT_METHOD_CRON
            ) {
                continue;
            }

            $orders = $this->getOrders($storeId);
            $response[$storeId] = $this->processOrders($orders, $storeId);
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getStores()
    {
        $stores = $this->storeRepository->getList();
        $storeList = array();
        foreach ($stores as $store) {
            $storeId = $store["store_id"];
            $storeName = $store["name"];
            $storeList[$storeId] = $storeName;
        }

        return $storeList;
    }

    /**
     * @param integer $storeId
     * @return array
     */
    public function getOrders($storeId)
    {
        $turnaroundTime = $this->dataHelper->getTurnaroundTime($storeId);
        $fromDate = $this->date->gmtDate(
            'Y-m-d H:i:s',
            strtotime('-' . $turnaroundTime . ' days')
        );
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('store_id', $storeId, 'eq')
            ->addFilter('created_at', $fromDate, 'gteq')
            ->create();
        $orderList = $this->orderRepository->getList($searchCriteria)->getItems();

        return $orderList;
    }

    /**
     * @param array $orders
     * @param integer $storeId
     * @return array
     */
    public function processOrders($orders, $storeId)
    {
        $response = [];
        foreach ($orders as $order) {
            $statuses = explode(',', $this->dataHelper->getOrderStatus($storeId));
            if ((is_array($statuses) && !empty($statuses) && !in_array($order->getStatus(), $statuses))) {
                continue;
            }

            $orderData = $this->orderDatahelper->getRequiredFields($order, $storeId);
            if ($orderData != '') {
                $response[$order->getIncrementId()] = $this->orderDatahelper->sendOrderData($orderData);
            }
        }

        return $response;
    }
}
