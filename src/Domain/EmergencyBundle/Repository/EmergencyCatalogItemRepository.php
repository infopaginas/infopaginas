<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class EmergencyCatalogItemRepository extends EntityRepository
{
    /**
     * @param bool $orderCategoryByAlphabet
     *
     * @return array
     */
    public function getCatalogItemWithContent($orderCategoryByAlphabet)
    {
        $qb = $this->createQueryBuilder('ci')
            ->join('ci.area', 'a')
            ->join('ci.category', 'ca')
            ->where('ci.hasContent = true')
            ->andWhere('ci.category IS NOT NULL')
            ->orderBy('a.position')
            ->addOrderBy('a.id')
        ;

        if ($orderCategoryByAlphabet) {
            $qb->addOrderBy('ca.searchName');
        } else {
            $qb->addOrderBy('ca.position');
        }

        $qb->addOrderBy('ca.id');

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

    /**
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     *
     * @return mixed
     */
    public function setContentUpdated($area, $category)
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->update('DomainEmergencyBundle:EmergencyCatalogItem', 'ci')
            ->where('ci.area = :area')
            ->andWhere('ci.category = :category')
            ->set('ci.contentUpdatedAt', ':date')
            ->setParameter('area', $area)
            ->setParameter('category', $category)
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->execute()
        ;

        return $result;
    }

    /**
     * Get business filter characters
     *
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     *
     * @return array
     */
    public function getCatalogItemFilterCharacters($area, $category)
    {
        $qb = $this->createQueryBuilder('ci');

        $qb
            ->select('ci.filters')
            ->where('ci.area = :area')
            ->andWhere('ci.category = :category')
            ->setParameter('area', $area)
            ->setParameter('category', $category)
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
