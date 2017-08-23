<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use FOS\UserBundle\Model\UserInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\QueryBuilder;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Oxa\GeolocationBundle\Utils\GeolocationUtils;
use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Component\Config\Definition\Builder\ExprBuilder;
use Doctrine\Common\Collections\Criteria;

/**
 * BusinessProfileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessProfileRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param UserInterface $user
     * @return array
     */
    public function findUserBusinessProfiles(UserInterface $user)
    {
        $businessProfiles = $this->findBy([
            'user' => $user,
            'isActive' => true,
        ]);

        return $businessProfiles;
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function findBusinessProfilesReviewedByUser(UserInterface $user)
    {
        $queryBuilder = $this->createQueryBuilder('bp')
            ->select('bp business, bp.slug')
            ->join('bp.businessReviews', 'br')
            ->where('br.user = :user')
            ->andWhere('bp.isActive = TRUE')
            ->setParameter('user', $user)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array     $ids
     * @param string    $locale
     *
     * @return array
     */
    public function findBusinessProfilesByIdsArray($ids, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $qb = $this->createQueryBuilder('bp')
            ->where('bp.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        $query = $qb->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder('bp');

        $queryBuilder->select('bp')
            ->from('DomainBusinessBundle:BusinessProfile', 'bp')
            ->andWhere('bp.isActive = TRUE')
            ->groupBy('bp.id')
        ;

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param int $limit
     * @param int $offset
     */
    protected function addLimitOffsetQueryBuilder(QueryBuilder $queryBuilder, $limit, $offset)
    {
        return $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;
    }

    /**
     * Get business profiles which do not have active subscription
     * @return BusinessProfile[]|null
     */
    public function getBusinessWithoutActiveSubscription()
    {
        $activeSubscriptionQb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b')
            ->from('DomainBusinessBundle:BusinessProfile', 'b')
            ->leftJoin('b.subscriptions', 's')
            ->andWhere('s.status = ' . StatusInterface::STATUS_ACTIVE)
        ;

        $qb = $this->getEntityManager()->createQueryBuilder();

        $objects = $qb
            ->select('bp')
            ->from('DomainBusinessBundle:BusinessProfile', 'bp')
            ->andWhere($qb->expr()->notIn('bp', $activeSubscriptionQb->getDQL()))
            ->getQuery()
            ->getResult()
        ;

        return $objects;
    }

    public function getHomepageVideos($limit)
    {
        $qb = $this->getVideosQuery()->setMaxResults($limit);

        $results = new Paginator($qb, $fetchJoin = false);

        return $results;
    }

    public function getVideos()
    {
        $qb = $this->getVideosQuery();

        $results = new Paginator($qb, $fetchJoin = false);

        return $results;
    }

    private function getVideosQuery()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('v')
            ->from(BusinessProfile::class, 'bp')
            ->innerJoin('bp.subscriptions', 'bp_s')
            ->innerJoin('bp_s.subscriptionPlan', 'bps_p')
            ->innerJoin(VideoMedia::class, 'v', Join::WITH, 'bp.video = v')
            ->where('bp.isActive = TRUE')
            ->andWhere('bps_p.code >= :platinumPlanCode')
            ->setParameter('platinumPlanCode', SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM)
            ->orderBy('v.createdAt', 'DESC')
        ;

        return $qb;
    }

    public function getBusinessProfilesByVideosUpdateQb()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('bp')
            ->from(BusinessProfile::class, 'bp')
            ->innerJoin('bp.subscriptions', 'bp_s')
            ->innerJoin('bp_s.subscriptionPlan', 'bps_p')
            ->innerJoin(VideoMedia::class, 'v', Join::WITH, 'bp.video = v')
            ->where('bp.isActive = TRUE')
            ->andWhere('bps_p.code >= :platinumPlanCode')
            ->setParameter('platinumPlanCode', SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM)
        ;

        return $queryBuilder;
    }

    public function getBusinessProfilesByVideosUpdate($searchParams, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $limit  = $searchParams->limit;
        $offset = ($searchParams->page - 1) * $limit;

        $queryBuilder = $this->getBusinessProfilesByVideosUpdateQb();
        $queryBuilder->orderBy('v.updatedAt', 'DESC');

        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);

        $query = $queryBuilder->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
    }

    public function countBusinessProfilesByVideosUpdate()
    {
        $queryBuilder = $this->getBusinessProfilesByVideosUpdateQb();

        $queryBuilder->select('count(bp.id) as rows');

        $results = $queryBuilder->getQuery()->getResult();

        return count($results);
    }

    protected function addSearchByCatalogCategoryQueryBuilder(QueryBuilder $queryBuilder, $category)
    {
        return $queryBuilder
            ->join('bp.categories', 'c')
            ->andWhere('c.id = :category')
            ->setParameter('category', $category);
    }

    protected function addSearchByCatalogLocalityQueryBuilder(QueryBuilder $queryBuilder, $locality)
    {
        return $queryBuilder
            ->andWhere('bp.catalogLocality = :locality')
            ->setParameter('locality', $locality);
    }

    /**
     * Counting search results
     *
     * @param SearchDTO $searchParams
     *
     * @return int
     */
    public function countCatalogSearchResults(SearchDTO $searchParams)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->addCatalogSearchQueryBuilder($queryBuilder, $searchParams);

        $queryBuilder->select('count(bp.id) as rows');

        $results = $queryBuilder->getQuery()->getResult();

        return count($results);
    }

    /**
     * add catalog search query
     *
     * @param QueryBuilder $queryBuilder
     * @param SearchDTO    $searchParams
     */
    protected function addCatalogSearchQueryBuilder($queryBuilder, SearchDTO $searchParams)
    {
        $category = $searchParams->getCategory();
        $catalogLocality = $searchParams->getCatalogLocality();

        if ($catalogLocality) {
            $this->addSearchByCatalogLocalityQueryBuilder($queryBuilder, $catalogLocality);

            if ($category) {
                $this->addSearchByCatalogCategoryQueryBuilder($queryBuilder, $category);
            }
        }
    }

    public function findBySlug($businessProfileSlug, $customSlug = false)
    {
        $query = $this->getQueryBuilder()
            ->where('bp.slug = :businessProfileSlug')
            ->setParameter('businessProfileSlug', $businessProfileSlug)
            ->setMaxResults(1)
        ;

        if ($customSlug) {
            $query->orWhere('bp.slug = :customSlug')
                ->orWhere('bp.slugEn = :customSlug')
                ->orWhere('bp.slugEs = :customSlug')
                ->setParameter('customSlug', $customSlug)
            ;
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @return IterableResult
     */
    public function getActiveBusinessProfilesIterator()
    {
        $qb = $this->getQueryBuilder();

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    /**
     * @return IterableResult
     */
    public function getUpdatedBusinessProfilesIterator()
    {
        $qb = $this->createQueryBuilder('bp')
            ->andWhere('bp.isUpdated = TRUE')
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    /**
     * Set isUpdated flag for all categories for elastic search synchronization
     *
     * @return mixed
     */
    public function setUpdatedAllBusinessProfiles()
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->update('DomainBusinessBundle:BusinessProfile', 'bp')
            ->where('bp.isActive = true')
            ->set('bp.isUpdated', ':isUpdated')
            ->setParameter('isUpdated', true)
            ->getQuery()
            ->execute()
        ;

        return $result;
    }

    /**
     * workaround for unstable EntityManagerInterface#flush() inside of event lintener
     *
     * @param $id
     *
     * @return mixed
     */
    public function setUpdatedBusinessProfile($id)
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->update('DomainBusinessBundle:BusinessProfile', 'bp')
            ->where('bp.isActive = TRUE')
            ->andWhere('bp.id = :id')
            ->set('bp.isUpdated', ':isUpdated')
            ->setParameter('isUpdated', true)
            ->setParameter('id', $id)
            ->getQuery()
            ->execute()
        ;

        return $result;
    }

    /**
     * @return IterableResult
     */
    public function getBusinessesWithoutActiveSubscriptionIterator()
    {
        $qb = $this->getBusinessesAndActiveSubscriptionQb();

        $qb->andWhere('s.id IS NULL');

        $businessProfileIds = $qb->getQuery()->getArrayResult();

        $businessesIterator = $this->getBusinessesIteratorByIds($businessProfileIds);

        return $businessesIterator;
    }

    /**
     * @return IterableResult
     */
    public function getBusinessProfilesWithoutCategoriesIterator()
    {
        $qb = $this->createQueryBuilder('bp')
            ->andWhere('bp.categories IS EMPTY')
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }

    /**
     * @return IterableResult
     */
    public function getBusinessesWithMultipleActiveSubscriptionsIterator()
    {
        $qb = $this->getBusinessesAndActiveSubscriptionQb();

        $qb
            ->groupBy('bp.id')
            ->having('COUNT(s.id) > 1')
        ;

        $businessProfileIds = $qb->getQuery()->getArrayResult();

        $businessesIterator = $this->getBusinessesIteratorByIds($businessProfileIds);

        return $businessesIterator;
    }

    protected function getBusinessesAndActiveSubscriptionQb()
    {
        $qb = $this->createQueryBuilder('bp')
            ->select('bp.id')
            ->distinct()
            ->leftJoin('bp.subscriptions', 's', 'WITH', 's.status = ' . StatusInterface::STATUS_ACTIVE)
            ->andWhere('bp.isActive = TRUE')
        ;

        return $qb;
    }

    /**
     * @param array $ids
     *
     * @return IterableResult
     */
    protected function getBusinessesIteratorByIds($ids)
    {
        $qb = $this->createQueryBuilder('bp')
            ->select('bp')
            ->andWhere('bp.id IN (:businessProfileIds)')
            ->setParameter('businessProfileIds', $ids)
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());
        $query->setParameter('businessProfileIds', $ids);

        return $query->iterate();
    }

    /**
     * @return IterableResult
     */
    public function getBusinessesWithTextWorkingHoursIterator()
    {
        $qb = $this->createQueryBuilder('bp')
            ->select('bp')
            ->where('bp.workingHours IS NOT NULL')
            ->andWhere('bp.workingHours != \'\'')
            ->orderBy('bp.id')
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());

        return $query->iterate();
    }
}
