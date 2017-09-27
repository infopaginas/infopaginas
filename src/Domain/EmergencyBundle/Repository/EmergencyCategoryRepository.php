<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class EmergencyCategoryRepository extends EntityRepository
{
    /**
     * @return IterableResult
     */
    public function getAllCategoriesIterator()
    {
        $qb = $this->createQueryBuilder('l');

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }
}
