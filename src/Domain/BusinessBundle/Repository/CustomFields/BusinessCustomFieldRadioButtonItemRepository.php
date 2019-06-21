<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;

class BusinessCustomFieldRadioButtonItemRepository extends EntityRepository
{
    public function getRadioButtonValuesByIds($radioButtonIds)
    {
        $queryBuilder = $this->createQueryBuilder('bcfrbi')
            ->join('bcfrbi.businessCustomFieldRadioButton', 'bcfrb')
            ->where('bcfrb.id = (:radioButtonIds)')
            ->setParameter('radioButtonIds', $radioButtonIds);

        return $queryBuilder->getQuery()->getResult();
    }
}
