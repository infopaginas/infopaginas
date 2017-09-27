<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class EmergencyCatalogItemRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getCatalogItemWithContent()
    {
        $qb = $this->createQueryBuilder('ci')
            ->join('ci.area', 'a')
            ->join('ci.category', 'ca')
            ->where('ci.hasContent = true')
            ->andWhere('ci.category IS NOT NULL')
            ->orderBy('a.position')
            ->addOrderBy('a.id')
            ->addOrderBy('ca.position')
            ->addOrderBy('ca.id')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return IterableResult
     */
    public function getCatalogItemsWithContentIterator()
    {
        $qb = $this->createQueryBuilder('ci');

        $qb->andWhere('ci.hasContent = TRUE');
        $qb->andWhere('ci.category IS NOT NULL');

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }
}
