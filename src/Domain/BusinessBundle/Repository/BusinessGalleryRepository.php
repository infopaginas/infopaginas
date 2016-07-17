<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 17.07.16
 * Time: 16:13
 */

namespace Domain\BusinessBundle\Repository;

use Domain\BusinessBundle\Entity\BusinessProfile;

/**
 * Class BusinessGalleryRepository
 * @package Domain\BusinessBundle\Repository
 */
class BusinessGalleryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function findBusinessProfileRemovedImages(BusinessProfile $businessProfile)
    {
        $this->getEntityManager()->getFilters()->disable('softdeleteable');

        $qb = $this->createQueryBuilder('bg');

        $images = $qb->where('bg.businessProfile = :businessProfile')
            ->andWhere('bg.deletedAt is not NULL')
            ->setParameter('businessProfile', $businessProfile)
            ->getQuery()
            ->getResult();

        $this->getEntityManager()->getFilters()->enable('softdeleteable');

        return $images;
    }
}
