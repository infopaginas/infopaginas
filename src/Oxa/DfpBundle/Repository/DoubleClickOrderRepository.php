<?php

namespace Oxa\DfpBundle\Repository;

/**
 * DoubleClickOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoubleClickOrderRepository extends \Doctrine\ORM\EntityRepository
{
    public function getOrdersIndexedByDcOrderId()
    {
        $qb = $this->createQueryBuilder('dc_o');
        $qb->indexBy('dc_o', 'dc_o.doubleClickOrderId');

        return $qb->getQuery()->getResult();
    }

    public function getDoubleClickOrderIds()
    {
        $qb = $this->createQueryBuilder('dc_o')
            ->select('dc_o.doubleClickOrderId')
            ->indexBy('dc_o', 'dc_o.doubleClickOrderId');

        return array_keys($qb->getQuery()->getArrayResult());
    }
}