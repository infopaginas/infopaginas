<?php

namespace Domain\BusinessBundle\Repository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * SubscriptionPlanRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubscriptionPlanRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSubscriptionStatistics()
    {
        $qb = $this->createQueryBuilder('subscription_plan');
        $qb->select('subscription_plan')
            ->addSelect('count(subscriptions.id) as cnt')
            ->leftJoin('subscription_plan.subscriptions', 'subscriptions', Join::LEFT_JOIN)
            ->andWhere('subscriptions.isActive = True')
            ->groupBy('subscription_plan');

        return $qb->getQuery()->getResult();
    }
}
