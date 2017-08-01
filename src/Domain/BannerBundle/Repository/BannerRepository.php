<?php

namespace Domain\BannerBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Domain\BannerBundle\Entity\Banner;

/**
 * BannerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BannerRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return QueryBuilder
     */
    protected function getBannerQueryBuilder()
    {
        return $this->createQueryBuilder('b')
            ->where('b.isActive = true');
    }

    /**
     * @param array $codes
     *
     * @return array
     */
    public function getBannersByTypeCodes(array $codes)
    {
        return $this->getBannerQueryBuilder()
            ->andWhere('b.code IN (:codes)')
            ->setParameter('codes', $codes)
            ->getQuery()
            ->getResult();
    }
}
