<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\CustomFields\BusinessCustomFieldRadioButtonAdmin;

class BusinessCustomFieldRadioButtonCollectionRepository extends EntityRepository
{
    public function getBusinessProfileNames($id)
    {
        $qb = $this->createQueryBuilder('bcfrbcr')
            ->distinct()
            ->select('bp.name')
            ->join('bcfrbcr.radioButtons', 'c')
            ->join('bcfrbcr.businessProfile', 'bp')
            ->where('bcfrbcr.radioButtons = :id')
            ->setParameter('id', $id)
            ->setMaxResults(BusinessCustomFieldRadioButtonAdmin::MAX_BUSINESS_NAMES_SHOW)
        ;

        return array_map('current', $qb->getQuery()->getResult());
    }

    public function countBusinesses($id)
    {
        $qb = $this->createQueryBuilder('bcfrbcr')
            ->select('COUNT(DISTINCT bp)')
            ->join('bcfrbcr.radioButtons', 'c')
            ->join('bcfrbcr.businessProfile', 'bp')
            ->where('bcfrbcr.radioButtons = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}
