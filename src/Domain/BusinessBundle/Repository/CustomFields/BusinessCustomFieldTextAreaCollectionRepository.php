<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\CustomFields\BusinessCustomFieldTextAreaAdmin;

class BusinessCustomFieldTextAreaCollectionRepository extends EntityRepository
{
    public function getBusinessProfileNames($id)
    {
        $qb = $this->createQueryBuilder('bcftacr')
            ->distinct()
            ->select('bp.name')
            ->join('bcftacr.textAreas', 'c')
            ->join('bcftacr.businessProfile', 'bp')
            ->where('bcftacr.textAreas = :id')
            ->setParameter('id', $id)
            ->setMaxResults(BusinessCustomFieldTextAreaAdmin::MAX_BUSINESS_NAMES_SHOW)
        ;

        return array_map('current', $qb->getQuery()->getResult());
    }

    public function countBusinesses($id)
    {
        $qb = $this->createQueryBuilder('bcftacr')
            ->select('COUNT(DISTINCT bp)')
            ->join('bcftacr.textAreas', 'c')
            ->join('bcftacr.businessProfile', 'bp')
            ->where('bcftacr.textAreas = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}
