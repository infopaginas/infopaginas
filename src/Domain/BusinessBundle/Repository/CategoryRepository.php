<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;

class CategoryRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function getAvailableCategoriesQb()
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isActive = TRUE')
            ->orderBy('c.name');

        return $qb;
    }

    /**
     * @param Locality  $locality
     * @param string    $locale
     */
    public function getAvailableCategoriesWithContent($locality, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $qb = $this->getAvailableCategoriesQb();

        $qb
            ->leftJoin('c.catalogItems', 'ci')
            ->andWhere('ci.hasContent = TRUE')
            ->andWhere('ci.locality = :locality')
            ->setParameter('locality', $locality)
            ->orderBy('c.searchText' . ucfirst($locale))
        ;

        $query = $qb->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
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

    /**
     * @return Category[]
     */
    public function getAvailableCategories()
    {
        $qb = $this->getAvailableCategoriesQb();

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ids
     *
     * @return Category[]
     */
    public function getAvailableCategoriesByIds($ids)
    {
        $qb = $this->getAvailableCategoriesQb()
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function getAvailableCategoryNameByIds($ids)
    {
        $qb = $this->getAvailableCategoriesQb()
            ->select('c.name, c.id')
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @return QueryBuilder
     */
    protected function getCategoryQueryBuilder()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('DomainBusinessBundle:Category', 'c');

        return $queryBuilder;
    }

    /**
     * @param array     $businessIdList
     * @param string    $locale
     *
     * @return Category[]
     */
    public function getCategoryByBusinessesIds(array $businessIdList, $locale)
    {
        $qb = $this->getCategoryQueryBuilder()
            ->join('c.businessProfiles', 'bp')
            ->where('bp.id in (:ids)')
            ->setParameter('ids', $businessIdList)
            ->orderBy('c.name')
        ;

        $query = $qb->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
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

    /**
     * @param string $categorySlug
     * @param string|bool $customSlug
     *
     * @return Category|null
     */
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

        $query->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $customSlug
     *
     * @return Category|null
     */
    public function getCategoryByCustomSlug($customSlug)
    {
        $query = $this->getAvailableCategoriesQb()
            ->andWhere('c.slugEn = :customSlug OR c.slugEs = :customSlug')
            ->setParameter('customSlug', $customSlug)
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $categoriesSlugs
     *
     * @return Category[]
     */
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
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $name
     *
     * @return Category[]|null
     */
    public function getCategoriesByName($name)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $result = $queryBuilder
            ->where(
                $queryBuilder->expr()->like('lower(c.name)', ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery()
            ->getResult();

        return $result;
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
