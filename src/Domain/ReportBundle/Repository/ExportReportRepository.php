<?php

namespace Domain\ReportBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Domain\ReportBundle\Entity\ExportReport;

class ExportReportRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string $status
     *
     * @return IterableResult
     */
    public function getExportReportByStatusIterator($status)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->setParameter('status', $status)
            ->orderBy('e.id')
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());
        $query->setParameter('status', $status);

        return $query->iterate();
    }

    /**
     * @param int    $id
     * @param string $status
     * @param array  $links
     * @return mixed
     */
    public function setExportReportData($id, $status, $links)
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()
            ->update(ExportReport::class, 'er')
            ->where('er.id = :id')
            ->setParameter('id', $id)
            ->set('er.status', ':status')
            ->setParameter('status', $status)
            ->set('er.links', ':links')
            ->setParameter('links', $links)
            ->getQuery()
            ->execute()
        ;

        return $result;
    }
}
