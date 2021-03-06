<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;

class CatalogItemRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param Locality $locality
     * @param Category $category
     *
     * @return array
     */
    public function getCountCatalogItemContent($locality, $category)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(DISTINCT bp.id)')
            ->from('DomainBusinessBundle:BusinessProfile', 'bp')
            ->join('bp.categories', 'c')
            ->where('bp.isActive = TRUE')
            ->andWhere('bp.catalogLocality = :locality')
            ->andWhere('c = :category')
            ->setParameter('locality', $locality)
            ->setParameter('category', $category)
        ;

        try {
            $countCatalogItemContent = $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            $countCatalogItemContent = 0;
        }

        return $countCatalogItemContent;
    }

    /**
     * @param Locality $locality
     * @param Category|null $category
     *
     * @return array
     */
    public function checkCatalogItemHasContent($locality, $category = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('ci.hasContent')
            ->from('DomainBusinessBundle:CatalogItem', 'ci')
            ->andWhere('ci.locality = :locality')
            ->setParameter('locality', $locality)
        ;

        if ($category) {
            $qb
                ->andWhere('ci.category = :category')
                ->setParameter('category', $category)
            ;
        } else {
            $qb->andWhere('ci.category IS NULL');
        }

        try {
            $checkCatalogItemHasContent = $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            $checkCatalogItemHasContent = false;
        }

        return $checkCatalogItemHasContent;
    }

    /**
     * @return IterableResult
     */
    public function getCatalogItemsWithContentIterator()
    {
        $qb = $this->createQueryBuilder('ci');

        $qb->andWhere('ci.hasContent = TRUE');

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateCatalogItemsWithContent = $query->iterate();

        return $iterateCatalogItemsWithContent;
    }
}
