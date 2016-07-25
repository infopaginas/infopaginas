<?php

namespace Domain\ReportBundle\Repository;

use Domain\ReportBundle\Entity\CategoryReport;

/**
 * CategoryReportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryReportRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Used to be shown in export files
     * @var array
     */
    private $datePeriod = [
        'start' => '-',
        'end'   => '-',
    ];

    /**
     * @param int $categoryId
     * @return null|CategoryReport
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoryReportByCategoryId(int $categoryId)
    {
        $result = $this->createQueryBuilder('cr')
            ->select('cr')
            ->leftJoin('cr.category', 'crc')
            ->andWhere('crc.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCategoryVisitorsQuery(array $params = [])
    {
        $query = $this->getEntityManager()
            ->getRepository('DomainReportBundle:CategoryReportCategory')
            ->createQueryBuilder('crc')
            ->leftJoin('crc.categoryReport', 'cr')
            ->leftJoin('cr.category', 'c')
            ->groupBy('categoryId')
            ->orderBy('categoryVisitors', 'DESC')
        ;

        if (isset($params['perPage']) && isset($params['page'])) {
            $first = ( $params['page'] - 1 ) * $params['perPage'];
            $query->setMaxResults($params['perPage']);
            $query->setFirstResult($first);
        }

        if (
            isset($params['date']) &&
            isset($params['date']['start']) &&
            isset($params['date']['end'])
        ) {
            $startDate = new \DateTime($params['date']['start']);
            $endDate =  new \DateTime($params['date']['end']);

            $this->datePeriod = [
                'start' => $params['date']['start'],
                'end' => $params['date']['end'],
            ];

            $endDate->modify('1 day');

            $query->andWhere("crc.date >= :startDate")
                ->setParameter('startDate', $startDate)
            ;

            $query->andWhere("crc.date < :endDate")
                ->setParameter('endDate', $endDate)
            ;
        }

        return $query;
    }

    /**
     * Visitors for each category per date period
     *
     * @param array $params
     * @return array
     */
    public function getCategoryVisitors(array $params = [])
    {
        $query = $this->getCategoryVisitorsQuery($params);

        $query ->select(
            'c.id categoryId',
            'COUNT(crc) as categoryVisitors'
        );

        $queryResults = $query
            ->getQuery()
            ->getResult()
        ;

        // get category objects array with keys as its Id
        $categories = $this->getEntityManager()
            ->getRepository('DomainBusinessBundle:Category')
            ->createQueryBuilder('c', 'c.id')
            ->select('c')
            ->getQuery()
            ->getResult()
        ;

        $results = [
            'results' => [],
            'datePeriod' => $this->datePeriod
        ];

        // merge with category objects to get translations later
        foreach ($queryResults as $key => $result) {
            $results['results'][$key]['category']           = $categories[$result['categoryId']];
            $results['results'][$key]['categoryVisitors']   = $result['categoryVisitors'];
        }

        return $results;
    }
}
