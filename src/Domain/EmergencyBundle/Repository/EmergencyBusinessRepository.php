<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

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
}
