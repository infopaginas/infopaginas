<?php

namespace Domain\BusinessBundle\Repository;

use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\QueryBuilder;

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

    public function searchWithQueryBuilder($searchQuery, $location, $limit = 20, $offset = 0)
    {
        $searchQuery    = $this->splitPhraseToPlain($searchQuery);
        $searchLocation = $this->splitPhraseToPlain($location);

        $queryBuilder = $this->getQueryBuilder();

        $this->addFTSSearchQueryBuilder($queryBuilder, $searchQuery);
        $this->addRankQueryBuilder($queryBuilder);
        $this->addCityRankQueryBuilder($queryBuilder);

        $this->addCategoryRankQueryBuilder($queryBuilder);
        $this->addAreaRankQueryBuilder($queryBuilder, $searchLocation);

        $this->addLimitOffsetQueryBuilder($queryBuilder, $limit, $offset);
        $this->addOrderByCategoryRankQueryBuilder($queryBuilder);
        $this->addOrderByRankQueryBuilder($queryBuilder);
        $this->addOrderByCityRankQueryBuilder($queryBuilder);
        $this->addOrderByAreaRankQueryBuilder($queryBuilder);

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

    protected function addFTSSearchQueryBuilder(QueryBuilder &$queryBuilder, $searchQuery)
    {
        return $queryBuilder
            ->where('TSQUERY( bp.searchFts, :searchQuery) = true')
            ->setParameter('searchQuery', $searchQuery);
    }


    protected function addRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchFts, :searchQuery) as rank');
    }

    protected function addCityRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addSelect('TSRANK(bp.searchCityFts, :searchQuery) as rank_city');
    }

    protected function addCategoryRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->join('bp.categories', 'c')
            ->addSelect('MAX(TSRANK(c.searchFts, :searchQuery)) as rank_c')
            ->orWhere('TSQUERY( c.searchFts, :searchQuery) = true');
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

    protected function addOrderByRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addOrderBy('rank', 'DESC');
    }

    protected function addOrderByCategoryRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addOrderBy('rank_c', 'DESC');
    }

    protected function addOrderByCityRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addOrderBy('rank_city', 'DESC');
    }

    protected function addOrderByAreaRankQueryBuilder(QueryBuilder &$queryBuilder)
    {
        return $queryBuilder
            ->addOrderBy('rank_a', 'DESC');
    }
}
