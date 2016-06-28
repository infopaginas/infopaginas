<?php

namespace Domain\BannerBundle\Repository;

/**
 * BannerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BannerRepository extends \Doctrine\ORM\EntityRepository
{
    protected function getBannerQueryBuilder()
    {
        return $this->createQueryBuilder('b')
            ->where('b.isActive = true');
    }

    protected function getBannerByTypeQueryBuilder()
    {
        return $this->getBannerQueryBuilder()
            ->join('b.type', 'bt');
    }

    public function getBannerByTypeCode(int $code)
    {
        return $this->getBannerByTypeQueryBuilder()
            ->andWhere('bt.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();
    }
}