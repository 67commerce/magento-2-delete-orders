<?php
/**
 * SixtySeven
 *
 * @category   SixtySeven
 * @package    SixtySeven_DeleteOrders
 * @copyright  Copyright (c) SixtySeven (https://www.67commerce.com/)
 */

namespace SixtySeven\DeleteOrders\Controller\Adminhtml\Delete;

use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use SixtySeven\DeleteOrders\Helper\Delete as DeleteHelper;

/**
 * Class MassOrders
 * @package SixtySeven\DeleteOrders\Controller\Adminhtml\Delete
 */
class MassOrders extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SixtySeven_DeleteOrders::delete';

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var DeleteHelper
     */
    protected $helper;

    /**
     * MassOrders constructor.
     * @param Context $context
     * @param Filter $filter
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param DeleteHelper $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        DeleteHelper $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return Redirect|ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        foreach ($collection as $order) {
            $incrementId = $order->getIncrementId();
            try {
                $this->helper->deleteOrder($order->getId());
                $this->messageManager->addSuccessMessage(__(
                    'Order #%1 has been deleted successfully.',
                    $incrementId
                ));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__(
                        'Cannot delete order #%1. Please try again later.',
                            $incrementId
                    ));
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');
        return $resultRedirect;
    }
}