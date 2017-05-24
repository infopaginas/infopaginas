<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\Entity\LandingPageShortCut;

class LandingPageShortCutRepository extends EntityRepository
{
    /**
     * @return LandingPageShortCut[]
     */
    public function getAvailableShortCutItems()
    {
        $qb = $this->createQueryBuilder('sc')
            ->where('sc.isActive = TRUE')
            ->orderBy('sc.id');

        return $qb->getQuery()->getResult();
    }
}
