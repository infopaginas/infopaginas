<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\StatusInterface;

/**
 * BusinessProfilePhoneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessProfilePhoneRepository extends EntityRepository
{
    public function getSamePhonesCount($phone, array $excludedIds)
    {
        $qb = $this->createQueryBuilder('bpp');

        $qb
            ->select('count(bpp.id)')
            ->where('bpp.id NOT IN (:ids)')
            ->andWhere('bpp.phone = :phone')
            ->setParameter('ids', $excludedIds)
            ->setParameter('phone', $phone)
        ;

        $result = $qb->getQuery()->getSingleResult();

        return array_shift($result);
    }

    public function getPhoneNumbersOfPaidProfiles()
    {
        $qb = $this->createQueryBuilder('bpp');

        $qb
            ->select('bpp.phone as phone')
            ->join('bpp.businessProfile', 'bp')
            ->join('bp.subscriptions', 's', Join::WITH, 's.status = ' . StatusInterface::STATUS_ACTIVE)
            ->where('bp.isActive = TRUE')
            ->andWhere('s.subscriptionPlan > :subscription')
            ->setParameter('subscription', SubscriptionPlan::CODE_FREE)
        ;

        return array_column($qb->getQuery()->getScalarResult(), 'phone');
    }
}
