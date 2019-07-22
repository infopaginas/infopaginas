<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\CustomFields\BusinessCustomFieldListAdmin;

class BusinessCustomFieldListCollectionRepository extends EntityRepository
{
    public function getBusinessProfileNames($id)
    {
        $qb = $this->getBusinessProfilesQb($id)
            ->distinct()
            ->select('bp.name')
            ->setMaxResults(BusinessCustomFieldListAdmin::MAX_BUSINESS_NAMES_SHOW)
        ;

        return array_map('current', $qb->getQuery()->getResult());
    }

    public function countBusinesses($id)
    {
        $qb = $this->getBusinessProfilesQb($id)->select('COUNT(DISTINCT bp)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function getBusinessProfilesQb($id)
    {
        $qb = $this->createQueryBuilder('bcflcr')
            ->join('bcflcr.businessProfile', 'bp')
            ->where('bcflcr.lists = :id')
            ->setParameter('id', $id)
        ;

        return $qb;
    }
}
