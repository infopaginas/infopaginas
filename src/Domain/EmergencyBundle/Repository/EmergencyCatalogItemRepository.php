<?php

namespace Domain\EmergencyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Domain\EmergencyBundle\Entity\EmergencyArea;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;

class EmergencyCatalogItemRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getCatalogItemWithContent()
    {
        $qb = $this->createQueryBuilder('ci')
            ->join('ci.area', 'a')
            ->join('ci.category', 'ca')
            ->where('ci.hasContent = true')
            ->andWhere('ci.category IS NOT NULL')
            ->orderBy('a.position')
            ->addOrderBy('a.id')
            ->addOrderBy('ca.position')
            ->addOrderBy('ca.id')
        ;

        return $qb->getQuery()->getResult();
    }
}
