<?php

namespace Domain\BusinessBundle\Repository;

/**
 * LocalityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocalityRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAvailableLocalitiesQb()
    {
        $qb = $this->createQueryBuilder('l');
        return $qb;
    }
}