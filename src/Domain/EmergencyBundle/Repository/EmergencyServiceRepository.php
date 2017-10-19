<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class EmergencyServiceRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getServiceFilters()
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.useAsFilter = true')
            ->orderBy('s.position')
        ;

        return $qb->getQuery()->getResult();
    }
}
