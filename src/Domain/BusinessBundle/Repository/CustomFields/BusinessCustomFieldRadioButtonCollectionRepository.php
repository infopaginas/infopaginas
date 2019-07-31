<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Admin\CustomFields\BusinessCustomFieldRadioButtonAdmin;

class BusinessCustomFieldRadioButtonCollectionRepository extends EntityRepository
{
    public function getBusinessProfileNames($id)
    {
        $qb = $this->getBusinessProfilesQb($id)
            ->distinct()
            ->select('bp.name')
            ->setMaxResults(BusinessCustomFieldRadioButtonAdmin::MAX_BUSINESS_NAMES_SHOW)
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
        $qb = $this->createQueryBuilder('bcfrbcr')
            ->join('bcfrbcr.businessProfile', 'bp')
            ->where('bcfrbcr.radioButtons = :id')
            ->setParameter('id', $id)
        ;

        return $qb;
    }
}
