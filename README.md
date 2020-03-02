# magento2-ordergrid-columns
Added `coupon_code` and `discount_amount` columns to order grid in admin.

### I see a few variants how to solve this issue:
* Add after plugin to `Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory` - as I have already done
* Add 2 columns (`coupon_code` and `discount_amoun`) using `db_schema.xml` to `sales_order_grid` table, then use Magento `virtualType` for Resource model to join this fields from `sales_order` table.
Something like that:
```
<virtualType name="Magento\Sales\Model\ResourceModel\Order\Grid" type="Magento\Sales\Model\ResourceModel\Grid">
        <arguments>
            <argument name="joins" xsi:type="array">
                <item name="discount_case" xsi:type="array">
                    <item name="table" xsi:type="string">sales_order</item>
                    <item name="origin_column" xsi:type="string">increment_id</item>
                    <item name="target_column" xsi:type="string">increment_id</item>
                </item>
            </argument>
            <argument name="columns" xsi:type="array">
                <item name="coupon_code" xsi:type="string">sales_order.coupon_code</item>
                <item name="discount_amount" xsi:type="string">sales_order.discount_amount</item>
            </argument>
            <argument name="notSyncedDataProvider" xsi:type="object">Magento\Sales\Model\ResourceModel\Provider\NotSyncedOrderDataProvider</argument>
        </arguments>
    </virtualType>
```
And don't forget to add `Magento_Sales` module sequence into my module.
* Rewrite grid collection class `\Magento\Sales\Model\ResourceModel\Order\Grid\Collection` and add `sales_order` table as join with this columns
* Rewrite (this means use our DataProvide class which extends original and rewrite one function) or add plugin (`afterGetData()`) to DataProvider `\Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider`
* We can add Column control classes to define column values. Something like that (same for `discount_amount`):
```
<?php
/**
 * @author    Oleksandr Yarzhombek <devias.corp@gmail.com>
 * @created   02.03.20
 */
declare(strict_types=1);

namespace Devias\OrderGrid\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Column CouponCode.
 */
class CouponCode extends Column
{
    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                //TODO fill data to column
            }
        }

        return $dataSource;
    }
}
``` 
