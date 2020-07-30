<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class CSVImportFileRepository extends EntityRepository
{
    public function getUnprocessedCSVImportFileIterator(): IterableResult
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.isProcessed = FALSE')
        ;

        return $qb->getQuery()->iterate();
    }
}
