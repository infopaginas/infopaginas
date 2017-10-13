<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;

class EmergencyBusinessRepository extends EntityRepository
{
    /**
     * Counting emergency businesses in catalog branch
     *
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     *
     * @return int
     */
    public function countCatalogItemContent($area, $category)
    {
        $businessesCount = $this->count([
            'area'      => $area,
            'category'  => $category,
            'isActive'  => true
        ]);

        return (int)$businessesCount;
    }

    /**
     * Counting emergency businesses in catalog branch
     *
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     *
     * @return \Datetime|null
     */
    public function getCatalogItemContentLastUpdated($area, $category)
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->select('b.updatedAt')
            ->where('b.area = :area')
            ->andWhere('b.category = :category')
            ->setParameter('area', $area)
            ->setParameter('category', $category)
            ->setMaxResults(1)
            ->orderBy('b.updatedAt', 'DESC')
        ;

        $result = $qb->getQuery()->getOneOrNullResult();

        if (is_array($result)) {
            $lastUpdated = current($result);
        } else {
            $lastUpdated = null;
        }

        return $lastUpdated;
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
        $qb = $this->createQueryBuilder('b');

        $qb
            ->select('b.firstSymbol')
            ->addSelect('LENGTH(b.firstSymbol) as HIDDEN r')
            ->distinct()
            ->where('b.area = :area')
            ->andWhere('b.category = :category')
            ->setParameter('area', $area)
            ->setParameter('category', $category)
            ->orderBy('r')
            ->addOrderBy('b.firstSymbol')
        ;

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Counting emergency businesses in catalog branch
     *
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     * @param int               $limit
     * @param int               $page
     *
     * @return int
     */
    public function getBusinessByAreaAndCategory($area, $category, $limit, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->createQueryBuilder('b');

        $qb
            ->where('b.area = :area')
            ->andWhere('b.category = :category')
            ->setParameter('area', $area)
            ->setParameter('category', $category)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('b.name')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return IterableResult
     */
    public function getActiveBusinessIterator()
    {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.isUpdated = true')
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }

    /**
     * @return mixed
     */
    public function setUpdatedAllEmergencyBusinesses()
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->update('DomainEmergencyBundle:EmergencyBusiness', 'b')
            ->set('b.isUpdated', ':isUpdated')
            ->setParameter('isUpdated', true)
            ->getQuery()
            ->execute()
        ;

        return $result;
    }

    /**
     * @param array $ids
     *
     * @return EmergencyBusiness[]
     */
    public function getAvailableBusinessesByIds($ids)
    {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }
}
