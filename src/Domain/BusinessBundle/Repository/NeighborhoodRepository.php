<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * NeighborhoodRepository
 */
class NeighborhoodRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAvailableNeighborhoodsQb()
    {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.name');

        return $qb;
    }
}
