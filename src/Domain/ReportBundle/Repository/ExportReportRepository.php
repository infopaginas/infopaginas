<?php

namespace Domain\ReportBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;

class ExportReportRepository extends \Doctrine\ORM\EntityRepository
{
    /**
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
}
