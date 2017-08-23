<?php

namespace Domain\ArticleBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getArticlesQueryBuilder()
    {
        return $this->createQueryBuilder('a')
            ->where('a.isPublished = true')
            ->andWhere('a.activationDate < CURRENT_TIMESTAMP()')
            ->andWhere('a.expirationDate >= CURRENT_TIMESTAMP() OR a.expirationDate IS NULL');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getArticlesForHomepageQueryBuilder()
    {
        return $this->getArticlesQueryBuilder()
            ->andWhere('a.isOnHomepage = true')
            ->orderBy('a.activationDate', 'DESC');
    }

    /**
     * @param string $categorySlug
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getPublishedArticlesQueryBuilder(string $categorySlug = '')
    {
        $qb = $this->getArticlesQueryBuilder();

        if ($categorySlug) {
            $qb = $qb
                ->leftJoin('a.category', 'c')
                ->andWhere('c.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug);
        }

        return $qb;
    }

    /**
     * @param int       $limit
     * @param string    $locale
     *
     * @return Article[]
     */
    public function getArticlesForHomepage(int $limit, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $qb = $this->getArticlesForHomepageQueryBuilder()->setMaxResults($limit);

        $query = $qb->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
    }

    /**
     * @param string $categorySlug
     * @return array
     */
    public function getPublishedArticles(string $categorySlug)
    {
        return $this->getPublishedArticlesQueryBuilder($categorySlug)->getQuery()->getResult();
    }

    /**
     * @param AbstractDTO $paramsDTO
     * @return array
     */
    public function findPaginatedPublishedArticles(AbstractDTO $paramsDTO, string $categorySlug, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $limit  = $paramsDTO->limit;
        $offset = ($paramsDTO->page - 1) * $limit;

        $queryBuilder = $this->getPublishedArticlesQueryBuilder($categorySlug);
        $queryBuilder = $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('a.activationDate', 'DESC')
        ;

        $query = $queryBuilder->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
    }

    /**
     * @return IterableResult
     */
    public function getActiveArticlesIterator()
    {
        $qb = $this->getArticlesQueryBuilder();

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
    }
}
