<?php

namespace Domain\BusinessBundle\Repository\CustomFields;

use Doctrine\ORM\EntityRepository;

class BusinessCustomFieldListItemRepository extends EntityRepository
{
    public function getValuesByIds($listIds)
    {
        $queryBuilder = $this->createQueryBuilder('bcfli')
            ->join('bcfli.businessCustomFieldList', 'bcfl')
            ->where('bcfl.id = (:listIds)')
            ->setParameter('listIds', $listIds);

        return $queryBuilder->getQuery()->getResult();
    }
}
