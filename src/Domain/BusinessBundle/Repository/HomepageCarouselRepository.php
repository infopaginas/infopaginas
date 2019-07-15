<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HomepageCarouselRepository extends EntityRepository
{
    public function countRows()
    {
        return $this->createQueryBuilder('hc')->select('count(hc.id)')->getQuery()->getSingleScalarResult();
    }
}
