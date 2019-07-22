<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\CustomFields\BusinessCustomFieldCheckboxAdmin;

class BusinessCustomFieldCheckboxCollectionRepository extends EntityRepository
{
    public function getBusinessProfileNames($id)
    {
        $qb = $this->createQueryBuilder('bcfccr')
            ->distinct()
            ->select('bp.name')
            ->join('bcfccr.checkboxes', 'c')
            ->join('bcfccr.businessProfile', 'bp')
            ->where('bcfccr.checkboxes = :id')
            ->setParameter('id', $id)
            ->setMaxResults(BusinessCustomFieldCheckboxAdmin::MAX_BUSINESS_NAMES_SHOW)
        ;

        return array_map('current', $qb->getQuery()->getResult());
    }

    public function countBusinesses($id)
    {
        $qb = $this->createQueryBuilder('bcfccr')
            ->select('COUNT(DISTINCT bp)')
            ->join('bcfccr.checkboxes', 'c')
            ->join('bcfccr.businessProfile', 'bp')
            ->where('bcfccr.checkboxes = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}
