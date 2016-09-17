<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 11.09.16
 * Time: 17:27
 */

namespace Oxa\DfpBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Oxa\DfpBundle\Entity\DoubleClickLineItem;
use Oxa\DfpBundle\Service\Google\LineItemService;

/**
 * Class DoubleClickLineItemsManager
 * @package Oxa\DfpBundle\Manager
 */
class DoubleClickLineItemsManager
{
    protected $entityManager;
    protected $lineItemService;
    protected $orderManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        DoubleClickOrdersManager $orderManager,
        LineItemService $lineItemService
    ) {
        $this->entityManager = $entityManager;
        $this->lineItemService = $lineItemService;
        $this->orderManager = $orderManager;
    }

    public function synchronizeDoubleClickLineItems()
    {
        $orders = $this->getDoubleClickOrdersManager()->getOrdersIndexedByDcOrderId();

        $orderIds = array_keys($orders);
        $lineItems = $this->getDoubleClickLineItemService()->getLineItemIdsAsOrderPair($orderIds);

        $alreadySynchronizedLineItemIds = $this->getDoubleClickLineItemRepository()->getDoubleClickLineItemIds();

        foreach ($lineItems as $lineItemId => $orderId) {
            //skip already synchronized elements
            if (in_array($lineItemId, $alreadySynchronizedLineItemIds)) {
                continue;
            }

            $lineItem = new DoubleClickLineItem();
            $lineItem->setDoubleClickLineItemId($lineItemId);
            $lineItem->setDoubleClickOrder($orders[$orderId]);

            $this->getEntityManager()->persist($lineItem);
        }

        $this->getEntityManager()->flush();
    }

    protected function getDoubleClickLineItemRepository()
    {
        return $this->getEntityManager()->getRepository(DoubleClickLineItem::class);
    }

    protected function getDoubleClickLineItemService() : LineItemService
    {
        return $this->lineItemService;
    }

    protected function getDoubleClickOrdersManager() : DoubleClickOrdersManager
    {
        return $this->orderManager;
    }

    protected function getEntityManager() : EntityManagerInterface
    {
        return $this->entityManager;
    }
}
