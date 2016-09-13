<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 11.09.16
 * Time: 16:19
 */

namespace Oxa\DfpBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Oxa\DfpBundle\Entity\DoubleClickOrder;
use Oxa\DfpBundle\Service\Google\OrderService;

class DoubleClickOrdersManager
{
    protected $entityManager;

    protected $companiesManager;

    protected $orderService;

    public function __construct(
        EntityManagerInterface $entityManager,
        DoubleClickCompaniesManager $companiesManager,
        OrderService $orderService
    ) {
        $this->entityManager = $entityManager;
        $this->companiesManager = $companiesManager;
        $this->orderService = $orderService;
    }

    public function getOrdersIndexedByDcOrderId()
    {
        return $this->getDoubleClickOrderRepository()->getOrdersIndexedByDcOrderId();
    }

    public function synchronizeDoubleClickOrders()
    {
        $advertisers = $this->getDoubleClickCompaniesManager()->getCompaniesIndexedByDcCompanyId();

        $advertiserIds = array_keys($advertisers);
        $orders = $this->getDoubleClickOrderService()->getOrderIdsAsAdvertiserPair($advertiserIds);

        $alreadySynchronizedOrderIds = $this->getDoubleClickOrderRepository()->getDoubleClickOrderIds();

        foreach ($orders as $orderId => $advertiserId) {
            //skip already synchronized items
            if (in_array($orderId, $alreadySynchronizedOrderIds)) {
                continue;
            }

            $doubleClickOrder = new DoubleClickOrder();
            $doubleClickOrder->setDoubleClickCompany($advertisers[$advertiserId]);
            $doubleClickOrder->setDoubleClickOrderId($orderId);

            $this->getEntityManager()->persist($doubleClickOrder);
        }

        $this->getEntityManager()->flush();
    }

    protected function getDoubleClickOrderRepository()
    {
        return $this->getEntityManager()->getRepository(DoubleClickOrder::class);
    }

    protected function getDoubleClickOrderService()
    {
        return $this->orderService;
    }

    protected function getDoubleClickCompaniesManager()
    {
        return $this->companiesManager;
    }

    protected function getEntityManager()
    {
        return $this->entityManager;
    }
}