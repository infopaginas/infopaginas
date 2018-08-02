<?php

namespace Domain\BusinessBundle\Repository;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileSuggestEdit;

/**
 * Class BusinessProfileSuggestEditRepository
 *
 * @package Domain\BusinessBundle\Repository
 */
class BusinessProfileSuggestEditRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param BusinessProfile $businessProfile
     *
     * @return array
     */
    public function getAggregatedDataByBusiness(BusinessProfile $businessProfile)
    {
        $qb = $this->createQueryBuilder('se');
        $qb->select('se.key as key, COUNT(se.id) as count')
            ->where('se.businessProfile = :businessProfile')
            ->andWhere('se.status = :newStatus')
            ->groupBy('se.key')
            ->setParameter('businessProfile', $businessProfile)
            ->setParameter('newStatus', BusinessProfileSuggestEdit::STATUS_NEW);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param                 $key
     *
     * @return mixed
     */
    public function getOpenedSuggestsByBusinessAndKey(BusinessProfile $businessProfile, $key)
    {
        $qb = $this->createQueryBuilder('se');
        $qb->where('se.businessProfile = :businessProfile')
            ->andWhere('se.key = :key')
            ->andWhere('se.status = :newStatus')
            ->setParameter('businessProfile', $businessProfile)
            ->setParameter('newStatus', BusinessProfileSuggestEdit::STATUS_NEW)
            ->setParameter('key', $key);

        return $qb->getQuery()->getResult();
    }
}
