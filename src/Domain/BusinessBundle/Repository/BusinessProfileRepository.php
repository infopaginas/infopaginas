<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Domain\BusinessBundle\Entity\BusinessProfile;
use FOS\UserBundle\Model\UserInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\QueryBuilder;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
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
    const SLUG = 'DomainBusinessBundle:BusinessProfile';

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
            'actualBusinessProfile' => null,
        ]);

        return $businessProfiles;
    }

    public function search(SearchDTO $searchParams)
    {
        $searchQuery        = $this->splitPhraseToPlain($searchParams->query);
        $searchLocation     = $this->splitPhraseToPlain($searchParams->locationValue->name);

        $limit  = $searchParams->limit;
        $offset = ($searchParams->page - 1 ) * $limit;

        $queryBuilder = $this->getQueryBuilder();

        $this->addSearchbByCategoryAndNameWithingAreaQueryBuilder($queryBuilder, $searchQuery, $searchLocation);
        $this->addDistanceBetweenPointsQueryBuilder($queryBuilder, $searchParams->locationValue);

        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);

        $this->addOrderByDistanceQueryBuilder($queryBuilder, Criteria::ASC);
        $this->addOrderBySubscriptionPlanQueryBuilder($queryBuilder, Criteria::DESC);

        $this->addOrderByCategoryRankQueryBuilder($queryBuilder, Criteria::DESC);
        $this->addOrderByRankQueryBuilder($queryBuilder, Criteria::DESC);

        if ($category = $searchParams->getCategory()) {
            $categoryFilter = $this->splitPhraseToPlain($category);
            $this->addCategoryFilterToQueryBuilder($queryBuilder, $categoryFilter);
        }

       

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }

    public function searchNeighborhood(SearchDTO $searchParams)
    {
        // TODO functionality

        return $this->search($searchParams);
    }

    public function searchAutosuggestWithBuilder($searchQuery, $limit = 5, $offset = 0)
    {
        $searchQuery    = $this->splitPhraseToPlain($searchQuery);

        $queryBuilder = $this->getQueryBuilder()
            ->addSelect('bp.name');

        $this->addFTSSearchQueryBuilder($queryBuilder, $searchQuery);
        $this->addRankQueryBuilder($queryBuilder);
        $this->addHeadlineToNameQueryBuilder($queryBuilder);
        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);
        $this->addOrderByRankQueryBuilder($queryBuilder);

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

    public function searchWithQueryBuilder(
        $searchQuery,
        $location,
        $categoryFilter = null,
        $neighborhoodFilter = null,
        $limit = 20,
        $offset = 0
    ) {
        $searchQuery    = $this->splitPhraseToPlain($searchQuery);
        $searchLocation = $this->splitPhraseToPlain($location);

        $queryBuilder = $this->getQueryBuilder();

        $this->addSearchbByCategoryAndNameWithingAreaQueryBuilder($queryBuilder, $searchQuery, $searchLocation);

        $this->addAreaRankQueryBuilder($queryBuilder, $searchLocation);

        $this->addCityRankQueryBuilder($queryBuilder);

        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);
        $this->addOrderByCategoryRankQueryBuilder($queryBuilder);
        $this->addOrderByRankQueryBuilder($queryBuilder);
        $this->addOrderByCityRankQueryBuilder($queryBuilder);
        $this->addOrderByAreaRankQueryBuilder($queryBuilder);

        if ($categoryFilter) {
            $categoryFilter = $this->splitPhraseToPlain($categoryFilter);
            $this->addCategoryFilterToQueryBuilder($queryBuilder, $categoryFilter);
        }


        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }

    protected function splitPhraseToPlain(string $phrase)
    {
        $words = explode(' ', $phrase);
        $wordParts = array_map(
            function ($item) {
                return $item . ":*";
            },
            $words
        );
        $plain = implode(' & ', $wordParts);

        return $plain;
    }

    protected function getQueryBuilder()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('bp')
            ->from('DomainBusinessBundle:BusinessProfile', 'bp')
            ->groupBy('bp.id');

        return $queryBuilder;
    }

    protected function addSearchbByCategoryAndNameWithingAreaQueryBuilder(
        QueryBuilder &$queryBuilder,
        $searchQuery,
        $location
    ) {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchFts, :searchQuery) as rank')
            ->join('bp.categories', 'c')
            ->join('bp.areas', 'a')
            ->addSelect('MAX(TSRANK(c.searchFts, :searchQuery)) as rank_c')
            ->where('TSQUERY( c.searchFts, :searchQuery) = true')
            ->orWhere('TSQUERY( bp.searchFts, :searchQuery) = true')
            ->andWhere('
                TSQUERY( a.searchFts, :searchLocation) = true
                OR
                TSQUERY( bp.searchCityFts, :searchLocation) = true
            ')
            ->setParameter('searchQuery', $searchQuery)
            ->setParameter('searchLocation', $location);
    }

    protected function addFTSSearchQueryBuilder(QueryBuilder &$queryBuilder, $searchQuery)
    {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchFts, :searchQuery) as rank')
            ->where('TSQUERY( bp.searchFts, :searchQuery) = true')
            ->andWhere('TSQUERY( a.searchFts, :searchLocation) = true')
            ->setParameter('searchQuery', $searchQuery);
    }

    protected function addCityRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchCityFts, :searchLocation) as rank_city')
            ->orWhere('TSQUERY( bp.searchCityFts, :searchLocation) = true');
    }

    protected function addCategoryRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->join('bp.categories', 'c')
            ->addSelect('MAX(TSRANK(c.searchFts, :searchQuery)) as rank_c')
            ->orWhere('TSQUERY( c.searchFts, :searchQuery) = true')
            ->andWhere('TSQUERY( a.searchFts, :searchLocation) = true');
    }

    protected function addAreaRankQueryBuilder(QueryBuilder &$queryBuilder, $location)
    {
        return $queryBuilder
            ->join('bp.areas', 'a')
            ->addSelect('MAX(TSRANK(a.searchFts, :searchLocation)) as rank_a')
            ->orWhere('TSQUERY( a.searchFts, :searchLocation) = true')
            ->setParameter('searchLocation', $location);
    }

    protected function addHeadlineToNameQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addSelect('TSHEADLINE(bp.name, :searchQuery ) as data');
    }

    protected function addLimitOffsetQueryBuilder(QueryBuilder &$queryBuilder, $limit, $offset)
    {
        return $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset);
    }

    protected function addOrderByRankQueryBuilder(QueryBuilder &$queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank', $order);
    }

    protected function addOrderByCategoryRankQueryBuilder(QueryBuilder &$queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank_c', $order);
    }

    protected function addOrderByCityRankQueryBuilder(QueryBuilder &$queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank_city', $order);
    }

    protected function addOrderByAreaRankQueryBuilder(QueryBuilder &$queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('rank_a', $order);
    }

    protected function addOrderByDistanceQueryBuilder(QueryBuilder &$queryBuilder, $order)
    {
        return $queryBuilder
            ->addOrderBy('distance', $order);
    }

    protected function addCategoryFilterToQueryBuilder(QueryBuilder &$queryBuilder, $category)
    {
        return $queryBuilder
            ->andWhere('TSQUERY( c.searchFts, :categoryFilter) = true')
            ->setParameter('categoryFilter', $category);
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
            ->addOrderBy('subscription', 'DESC');
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

    protected function addDistanceBetweenPointsQueryBuilder(QueryBuilder &$queryBuilder, LocationValueObject $location)
    {
        return $queryBuilder
            ->addSelect('2 * 6371 * sin (
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
                    ( 1 - cos( ( bp.longitude - :userLongitude ) * PI()/180 ) ) / 2 
                
                )
            ) AS distance')
            ->setParameter('userLatitude', $location->lat)
            ->setParameter('userLongitude', $location->lng);
    }
}
