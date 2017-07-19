<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\QueryBuilder;

/**
 * ZipRepository
 */
class ZipRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return IterableResult
     */
    public function getZipCodesIterator()
    {
        $qb = $this->getQueryBuilder();

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder('z');
    }
}
