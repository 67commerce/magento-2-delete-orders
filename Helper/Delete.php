<?php
/**
 * SixtySeven
 *
 * @category   SixtySeven
 * @package    SixtySeven_DeleteOrders
 * @copyright  Copyright (c) SixtySeven (https://www.67commerce.com/)
 */

namespace SixtySeven\DeleteOrders\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Sales\Model\Order;

/**
 * Class Delete
 * @package SixtySeven\DeleteOrders\Helper
 */
class Delete extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var OrderFactory
     */
    protected $orderResourceFactory;

    /**
     * @var Order
     */
    protected $order;


    /**
     * Delete constructor.
     * @param Context $context
     * @param OrderFactory $orderResourceFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderResourceFactory,
        Order $order
    ) {
        $this->orderResourceFactory   = $orderResourceFactory;
        $this->order = $order;

        parent::__construct($context);
    }

    /**
     * @param $orderId
     */
    public function deleteOrder($orderId)
    {
        $order = $this->order->load($orderId);
        $order->delete();

        $resourceConnection   = $this->orderResourceFactory->create();
        $connection = $resourceConnection->getConnection();

        $gridTables = [];
        $gridTables[] = $connection->getTableName('sales_invoice_grid');
        $gridTables[] = $connection->getTableName('sales_shipment_grid');
        $gridTables[] = $connection->getTableName('sales_creditmemo_grid');

        foreach ($gridTables as $tableName) {
            $connection->delete($tableName, $orderId);
        }

    }
}