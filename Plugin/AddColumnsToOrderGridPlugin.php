<?php
/**
 * @author    Oleksandr Yarzhombek <devias.corp@gmail.com>
 * @created   28.02.20
 */
declare(strict_types=1);

namespace Devias\OrderGrid\Plugin;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

/**
 * Add coupon_code and discount_amount data to order grid plugin.
 */
class AddColumnsToOrderGridPlugin
{
    /**
     * Order grid data provider name.
     *
     * @var string
     */
    public const SALES_ORDER_GRID_DATA_SOURCE = 'sales_order_grid_data_source';

    /**
     * After get report plugin.
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @param string $requestName
     *
     * @return \Magento\Framework\Data\Collection
     * @throws \Exception
     *
     * @see \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory::getReport()
     */
    public function afterGetReport(
        CollectionFactory $subject,
        Collection $collection,
        string $requestName
    ): \Magento\Framework\Data\Collection {
        if ($requestName !== self::SALES_ORDER_GRID_DATA_SOURCE) {
            return $collection;
        }

        $salesOrderTable = $collection->getTable('sales_order');
        /** @var \Magento\Framework\DB\Select $select */
        $select = $collection->getSelect();
        $select->joinLeft(
            ['so' => $salesOrderTable],
            'main_table.increment_id = so.increment_id',
            [OrderInterface::COUPON_CODE, OrderInterface::DISCOUNT_AMOUNT]
        );

        return $collection;
    }
}
