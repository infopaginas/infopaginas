<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Domain\BusinessBundle\Entity\Category;

class CategoryRepository extends EntityRepository
{
    public function getAvailableCategoriesQb()
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isActive = TRUE')
            ->orderBy('c.name');

        return $qb;
    }

    public function getAvailableCategoriesWithContent($locality, $locale = false)
    {
        $qb = $this->getAvailableCategoriesQb();

        $qb
            ->leftJoin('c.catalogItems', 'ci')
            ->andWhere('ci.hasContent = TRUE')
            ->andWhere('ci.locality = :locality')
            ->setParameter('locality', $locality)
        ;

        if ($locale) {
            $qb->orderBy('c.searchText' . ucfirst($locale));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return IterableResult
     */
    public function getAllCategoriesIterator()
    {
        $qb = $this->createQueryBuilder('c');

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateCategories = $query->iterate();

        return $iterateCategories;
    }

    public function getAvailableCategories()
    {
        $qb = $this->getAvailableCategoriesQb();

        return $qb->getQuery()->getResult();
    }

    public function getAvailableCategoriesByIds($ids)
    {
        $qb = $this->getAvailableCategoriesQb()
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }

    protected function getCategoryQueryBuilder()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('DomainBusinessBundle:Category', 'c');

        return $queryBuilder;
    }

    public function getCategoryByBusinessesIds(array $businessIdList)
    {
        $queryBuilder = $this->getCategoryQueryBuilder()
            ->join('c.businessProfiles', 'bp')
            ->where('bp.id in (:ids)')
            ->setParameter('ids', $businessIdList)
            ->orderBy('c.name')
        ;

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }

    /**
     * Count all categories
     *
     * @return mixed
     */
    public function getAllCategoriesCount()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from('DomainBusinessBundle:Category', 'c')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getCategoryBySlug($categorySlug, $customSlug = false)
    {
        $query = $this->getAvailableCategoriesQb()
            ->where('c.slug = :categorySlug')
            ->setParameter('categorySlug', $categorySlug)
        ;

        if ($customSlug) {
            $query->orWhere('c.slug = :customSlug')
                ->orWhere('c.slugEn = :customSlug')
                ->orWhere('c.slugEs = :customSlug')
                ->setParameter('customSlug', $customSlug)
            ;
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getCategoryByCustomSlug($customSlug)
    {
        $query = $this->getAvailableCategoriesQb()
            ->andWhere('c.slugEn = :customSlug OR c.slugEs = :customSlug')
            ->setParameter('customSlug', $customSlug)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getCategoriesBySlugs($categoriesSlugs)
    {
        $query = $this->getAvailableCategoriesQb()
            ->where('c.slug IN (:categoriesSlugs)')
            ->setParameter('categoriesSlugs', $categoriesSlugs)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * @param array $slugs
     *
     * @return Category|null
     */
    public function getCategoryByOldSlugs($slugs)
    {
        $query = $this->createQueryBuilder('c')
            ->orWhere('c.slug IN (:slugs)')
            ->orWhere('c.slugEn IN (:slugs)')
            ->orWhere('c.slugEs IN (:slugs)')
            ->setParameter('slugs', $slugs)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @return IterableResult
     */
    public function getAvailableCategoriesIterator()
    {
        $qb = $this->getAvailableCategoriesQb();

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    /**
     * @return IterableResult
     */
    public function getUpdatedCategoriesIterator()
    {
        $qb = $this->getAvailableCategoriesQb();
        $qb->andWhere('c.isUpdated = TRUE');

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    /**
     * Set isUpdated flag for all businesses for elastic search synchronization
     *
     * @return mixed
     */
    public function setUpdatedAllCategories()
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->update('DomainBusinessBundle:Category', 'c')
            ->where('c.isActive = TRUE')
            ->set('c.isUpdated', ':isUpdated')
            ->setParameter('isUpdated', true)
            ->getQuery()
            ->execute()
        ;

        return $result;
    }
}
