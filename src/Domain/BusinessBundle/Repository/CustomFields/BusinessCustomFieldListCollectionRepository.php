<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\CustomFields\BusinessCustomFieldListAdmin;

class BusinessCustomFieldListCollectionRepository extends EntityRepository
{
    public function getBusinessProfileNames($id)
    {
        $qb = $this->createQueryBuilder('bcflcr')
            ->distinct()
            ->select('bp.name')
            ->join('bcflcr.lists', 'c')
            ->join('bcflcr.businessProfile', 'bp')
            ->where('bcflcr.lists = :id')
            ->setParameter('id', $id)
            ->setMaxResults(BusinessCustomFieldListAdmin::MAX_BUSINESS_NAMES_SHOW)
        ;

        return array_map('current', $qb->getQuery()->getResult());
    }

    public function countBusinesses($id)
    {
        $qb = $this->createQueryBuilder('bcflcr')
            ->select('COUNT(DISTINCT bp)')
            ->join('bcflcr.lists', 'c')
            ->join('bcflcr.businessProfile', 'bp')
            ->where('bcflcr.lists = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}
