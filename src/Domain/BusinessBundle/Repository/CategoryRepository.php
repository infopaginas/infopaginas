<?php

namespace Domain\BusinessBundle\Repository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAvailableCategoriesQb()
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isActive = TRUE')
            ->orderBy('c.name');

        return $qb;
    }

    public function searchAutosuggest($name, string $locale)
    {
        $name    = $this->splitPhraseToPlain($name);
        $connection = $this->getEntityManager()->getConnection();

        $searchSQL = $this->getSearchSQLQuery($locale);

        $statement = $connection->prepare(
            "SELECT
                ts_headline(name, q) as data,
                name
            FROM
            (
                $searchSQL
            ) as search
            LIMIT 5"
        );

        $statement->bindValue("searchQuery", $name);
        $statement->execute();
        $results = $statement->fetchAll();

        return $results;
    }

    protected function getSearchSQLQuery(string $locale)
    {
        return 'SELECT
                c.id AS id,
                c.search_text_' . $locale . ' as name,
                q,
                ts_rank(c.search_fts_' . $locale . ', q) AS rank
            FROM
                category c,
                to_tsquery(:searchQuery) q
            WHERE
                c.search_fts_' . $locale . ' @@ q
            AND (
                c.deleted_at IS NULL
            )
            ORDER BY rank DESC';
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
        $plain = implode(' & ', $wordParts);

        return $plain;
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
        $query = 'SELECT c FROM DomainBusinessBundle:Category c JOIN c.businessProfiles bp WHERE bp.id IN (:ids)';
        $queryBuilder = $this->getEntityManager()->createQuery($query)
            ->setParameter('ids', $businessIdList);

        $results = $queryBuilder->getResult();

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
}
