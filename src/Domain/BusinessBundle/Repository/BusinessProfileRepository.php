<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use FOS\UserBundle\Model\UserInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\QueryBuilder;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Oxa\GeolocationBundle\Utils\GeolocationUtils;
use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\WistiaBundle\Entity\WistiaMedia;
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
     * @param int $id
     * @param string $locale
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findWithLocale(int $id, string $locale)
    {
        $qb = $this->createQueryBuilder('bp');

        $qb->select('bp')
            ->where('bp.id = :id')
            ->leftJoin('bp.categories', 'categories')
            ->setParameter('id', $id);

        $query = $qb->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        // Force the locale
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );

        return $query->getSingleResult();
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function findUserBusinessProfiles(UserInterface $user)
    {
        $businessProfiles = $this->findBy([
            'user' => $user,
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
     * @param $ids
     * @return array
     */
    public function findBusinessProfilesByIdsArray($ids)
    {
        $queryBuilder = $this->createQueryBuilder('bp')
            ->where('bp.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Main search functionality
     *
     * @param SearchDTO $searchParams
     * @param string    $locale
     * @return array
     */
    public function search(SearchDTO $searchParams, string $locale)
    {
        if (!$searchParams->locationValue) {
            return null;
        }

        $searchQuery = $this->splitPhraseToPlain($searchParams->query);

        $limit  = $searchParams->limit;
        $offset = ($searchParams->page - 1) * $limit;
        $queryBuilder = $this->getQueryBuilder();

        $this->addDistanceBetweenPointsQueryBuilder($queryBuilder, $searchParams->locationValue);
        $this->addSearchbByCategoryAndNameQueryBuilder($queryBuilder, $searchQuery, $locale);

        $this->addSearchByLocationQueryBuilder($queryBuilder, $searchParams);

        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);

        if (SearchDataUtil::ORDER_BY_DISTANCE == $searchParams->getOrderBy()) {
            $this->addOrderByDistanceQueryBuilder($queryBuilder, Criteria::ASC);
            $this->addOrderByRankQueryBuilder($queryBuilder, Criteria::DESC);
            $this->addOrderByCategoryRankQueryBuilder($queryBuilder, Criteria::DESC);
        } else {
            $this->addOrderByRankQueryBuilder($queryBuilder, Criteria::DESC);
            $this->addOrderByCategoryRankQueryBuilder($queryBuilder, Criteria::DESC);
            $this->addOrderByDistanceQueryBuilder($queryBuilder, Criteria::ASC);
        }

        $this->addOrderBySubscriptionPlanQueryBuilder($queryBuilder, Criteria::DESC);

        $category = $searchParams->getCategory();

        if ($category) {
            $this->addCategoryFilterToQueryBuilder($queryBuilder, $category);
        }

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }

    /**
     * Counting search results
     *
     * @param SearchDTO $searchParams
     * @param string $locale
     * @return int
     */
    public function countSearchResults(SearchDTO $searchParams, string $locale)
    {
        $searchQuery = $this->splitPhraseToPlain($searchParams->query);

        $queryBuilder = $this->getQueryBuilder();

        $this->addCountToSearchByCategoryAndNameWithingAreaQueryBuilder($queryBuilder, $searchQuery, $locale);

        $this->addSearchByLocationQueryBuilder($queryBuilder, $searchParams);

        $category = $searchParams->getCategory();

        if ($category) {
            $this->addCategoryFilterToQueryBuilder($queryBuilder, $category);
        }

        $results = $queryBuilder->getQuery()->getResult();

        return count($results);
    }

    public function searchAutosuggestWithBuilder($query, $locale, $limit = 5, $offset = 0)
    {
        $searchQuery = $this->splitPhraseToPlain($query);

        $queryBuilder = $this->getQueryBuilder()->addSelect('bp.name' . $locale . ' as name');

        $this->addSearchbByCategoryAndNameQueryBuilder($queryBuilder, $searchQuery, $locale);
        $this->addHeadlineToNameQueryBuilder($queryBuilder, $locale);
        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);
        $this->addOrderByRankQueryBuilder($queryBuilder, Criteria::DESC);

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

    protected function splitPhraseToPlain(string $phrase)
    {
        $words = explode(' ', $phrase);
        $words = array_filter($words);
        $wordParts = array_map(
            function ($item) {
                return $item . ":*";
            },
            $words
        );
        $plain = implode(' | ', $wordParts);

        return $plain;
    }

    protected function getEmptyQueryBuilder()
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

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

    protected function addSearchbByCategoryAndNameQueryBuilder(
        QueryBuilder $queryBuilder,
        string $searchQuery,
        string $locale
    ) {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchFts' . $locale . ', :searchQuery) as rank')
            ->join('bp.categories', 'c')
            ->addSelect('MAX(TSRANK(c.searchFts' . $locale . ', :searchQuery)) as rank_c')
            ->andWhere('(
                TSQUERY( c.searchFts' . $locale . ', :searchQuery) = true
                OR
                TSQUERY( bp.searchFts' . $locale . ', :searchQuery) = true
            )')
            ->setParameter('searchQuery', $searchQuery)
        ;
    }

    protected function addCountToSearchByCategoryAndNameWithingAreaQueryBuilder(
        QueryBuilder &$queryBuilder,
        string $searchQuery,
        string $locale
    ) {
        return $queryBuilder
            ->select('count(bp.id) as rows')
            ->join('bp.categories', 'c')
            ->andWhere('(
                TSQUERY( c.searchFts' . $locale . ', :searchQuery) = true
                OR
                TSQUERY( bp.searchFts' . $locale . ', :searchQuery) = true
            )')
            ->setParameter('searchQuery', $searchQuery)
        ;
    }

    protected function addSearchByLocationQueryBuilder(QueryBuilder $queryBuilder, $searchParams)
    {
        $searchString = '(
                (bp.serviceAreasType = :serviceAreasTypeArea
                    AND ' . $this->getDistanceBetweenPointsSql() . ' <= bp.milesOfMyBusiness)
                OR
                (bp.serviceAreasType = :serviceAreasTypeLoc
                    AND loc.id = :localityId)
            )';

        if ($searchParams->getNeighborhood()) {
            $queryBuilder->join('bp.neighborhoods', 'nei')
                ->andWhere('nei.id = :neighborhoodId')
                ->setParameter('neighborhoodId', $searchParams->getNeighborhood())
            ;
        }

        $location = $searchParams->locationValue;

        if ($location->locality) {
            $localityId = $location->locality->getId();
        } else {
            $localityId = 0;
        }

        $queryBuilder->leftJoin('bp.localities', 'loc')
            ->andWhere($searchString)
            ->setParameter('userLatitude', $location->lat)
            ->setParameter('userLongitude', $location->lng)
            ->setParameter('serviceAreasTypeArea', BusinessProfile::SERVICE_AREAS_AREA_CHOICE_VALUE)
            ->setParameter('serviceAreasTypeLoc', BusinessProfile::SERVICE_AREAS_LOCALITY_CHOICE_VALUE)
            ->setParameter('localityId', $localityId)
        ;

        return $queryBuilder;
    }

    protected function addCityRankQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchCityFts, :searchLocation) as rank_city')
            ->orWhere('TSQUERY( bp.searchCityFts, :searchLocation) = true')
        ;
    }

    protected function addCategoryRankQueryBuilder(QueryBuilder $queryBuilder, string $locale)
    {
        return $queryBuilder
            ->join('bp.categories', 'c')
            ->addSelect('MAX(TSRANK(c.searchFts' . $locale . ', :searchQuery)) as rank_c')
            ->orWhere('TSQUERY( c.searchFts' . $locale . ', :searchQuery) = true')
            ->andWhere('TSQUERY( loc.searchFts, :searchLocation) = true')
        ;
    }

    protected function addHeadlineToNameQueryBuilder(QueryBuilder $queryBuilder, $locale)
    {
        return $queryBuilder
            ->addSelect('TSHEADLINE(bp.name' . $locale . ', :searchQuery ) as data')
        ;
    }

    protected function addLimitOffsetQueryBuilder(QueryBuilder $queryBuilder, $limit, $offset)
    {
        return $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;
    }

    protected function addOrderByRankQueryBuilder(QueryBuilder $queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank', $order)
        ;
    }

    protected function addOrderByCategoryRankQueryBuilder(QueryBuilder $queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank_c', $order)
        ;
    }

    protected function addOrderByCityRankQueryBuilder(QueryBuilder $queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank_city', $order)
        ;
    }

    protected function addOrderByAreaRankQueryBuilder(QueryBuilder $queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank_a', $order)
        ;
    }

    protected function addOrderByDistanceQueryBuilder(QueryBuilder $queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('distance', $order)
        ;
    }

    protected function addCategoryFilterToQueryBuilder(QueryBuilder $queryBuilder, $category)
    {
        return $queryBuilder
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $category)
        ;
    }

    protected function addOrderBySubscriptionPlanQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addSelect('sp.rank as subscription')
            ->leftJoin('bp.subscriptions', 's')
            ->leftJoin('s.subscriptionPlan', 'sp')
            ->andWhere('s.status = :subscriptionStatus')
            ->setParameter('subscriptionStatus', StatusInterface::STATUS_ACTIVE)
            ->addGroupBy('sp.rank')
            ->addOrderBy('subscription', 'DESC')
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

    /**
     * Get business profiles ids array
     *
     * @param int|null $limit
     * @return BusinessProfile[]|null
     */
    public function getIndexedBusinessProfileIds(int $limit = null)
    {
        $query = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('bp.id')
            ->from('DomainBusinessBundle:BusinessProfile', 'bp', 'bp.id')
        ;

        if ($limit) {
            $query->setMaxResults($limit);
        }

        $result = $query
            ->getQuery()
            ->getResult()
        ;

        return array_keys($result);
    }

    /**
     * Adding distance value between points
     *
     * @param QueryBuilder &$queryBuilder
     * @param LocationValueObject $location
     *
     * @return queryBuilder
     */
    protected function addDistanceBetweenPointsQueryBuilder(QueryBuilder $queryBuilder, LocationValueObject $location)
    {
        return $queryBuilder
            ->addSelect($this->getDistanceBetweenPointsSql() . ' AS distance')
            ->setParameter('userLatitude', $location->lat)
            ->setParameter('userLongitude', $location->lng)
        ;
    }

    private function getDistanceBetweenPointsSql()
    {
        return '(' . GeolocationUtils::getEarthDiameterMiles() . ' * sin (
                sqrt (
                    ( 1 - cos (
                        (bp.latitude - :userLatitude) * PI()/180
                        )
                    ) / 2
                    +
                    cos (:userLatitude * PI()/180)
                    *
                    cos (bp.latitude * PI()/180)
                    *
                    ( 1 - cos( ( bp.longitude - :userLongitude ) * PI()/180 ) ) / 2)
                ))';
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

    public function getBusinessProfilesWithAllowedAdUnitsForFilter() : array
    {
        $qb = $this->createQueryBuilder('bp');
        $qb
            ->innerJoin('bp.subscriptions', 'bp_s', Join::INNER_JOIN)
            ->innerJoin('bp_s.subscriptionPlan', 'bps_s', Join::INNER_JOIN)
            ->where('bp.isActive = True')
            ->andWhere('bp_s.isActive = True')
            ->andWhere('bps_s.code >= :priorityPlanCode')
            ->setParameter('priorityPlanCode', SubscriptionPlanInterface::CODE_PRIORITY)
        ;

        $result = [];

        /** @var BusinessProfile $businessProfile */
        foreach ($qb->getQuery()->getResult() as $businessProfile) {
            $result[$businessProfile->getId()] = $businessProfile->getName();
        }

        return $result;
    }

    public function getBusinessProfilesForFilter()
    {
        $qb = $this->createQueryBuilder('bp')
            ->where('bp.isActive = True')
        ;

        $result = [];

        /** @var BusinessProfile $businessProfile */
        foreach ($qb->getQuery()->getResult() as $businessProfile) {
            $result[$businessProfile->getId()] = $businessProfile->getName();
        }

        return $result;
    }

    public function getBusinessProfilesWithAllowedAdUnits() : array
    {
        $qb = $this->createQueryBuilder('bp');
        $qb->where('bp.isActive = True');

        return $qb->getQuery()->getResult();
    }

    private function getVideosQuery()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('v')
            ->from(BusinessProfile::class, 'bp')
            ->innerJoin('bp.subscriptions', 'bp_s')
            ->innerJoin('bp_s.subscriptionPlan', 'bps_p')
            ->innerJoin(WistiaMedia::class, 'v', Join::WITH, 'bp.video = v')
            ->where('bp.isActive = TRUE')
            ->andWhere('bps_p.code = :platinumPlanCode')
            ->setParameter('platinumPlanCode', SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM)
            ->orderBy('v.createdAt', 'DESC')
        ;

        return $qb;
    }

    /**
     * Main search functionality
     *
     * @param SearchDTO $searchParams
     * @param string    $locale
     * @return array
     */
    public function searchCatalog(SearchDTO $searchParams, $locale)
    {
        if (!$searchParams->locationValue) {
            return null;
        }

        $limit  = $searchParams->limit;
        $offset = ($searchParams->page - 1) * $limit;
        $queryBuilder = $this->getQueryBuilder();

        $this->addDistanceBetweenPointsQueryBuilder($queryBuilder, $searchParams->locationValue);
        $this->addCatalogSearchQueryBuilder($queryBuilder, $searchParams);
        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);
        $this->addOrderBySubscriptionPlanQueryBuilder($queryBuilder);

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
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
        $category        = $searchParams->getCategory();
        $subcategory     = $searchParams->getSubcategory();
        $catalogLocality = $searchParams->getCatalogLocality();

        if ($catalogLocality) {
            $this->addSearchByCatalogLocalityQueryBuilder($queryBuilder, $catalogLocality);

            if ($category) {
                if ($subcategory) {
                    $this->addSearchByCatalogCategoryQueryBuilder($queryBuilder, $subcategory);
                } else {
                    $this->addSearchByCatalogCategoryQueryBuilder($queryBuilder, $category);
                }
            }
        }
    }
}
