<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class EmergencyAreaRepository extends EntityRepository
{
    /**
     * @return IterableResult
     */
    public function getAllAreasIterator()
    {
        $qb = $this->createQueryBuilder('l');

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }
}
