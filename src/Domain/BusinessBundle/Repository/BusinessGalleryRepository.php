<?php

namespace Domain\BusinessBundle\Repository;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;

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
    public function findBusinessProfileAdvertisementImages(BusinessProfile $businessProfile)
    {
        $qb = $this->createQueryBuilder('bg');

        $advertisements = $qb->where('bg.businessProfile = :businessProfile')
            ->andWhere('bg.type = :businessProfileAdvertisementImageType')
            ->andWhere('bg.isActive = true')
            ->setParameter('businessProfile', $businessProfile)
            ->setParameter('businessProfileAdvertisementImageType', OxaMediaInterface::CONTEXT_BANNER)
            ->getQuery()
            ->getResult();

        return $advertisements;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return array
     */
    public function findBusinessProfilePhotoImages(BusinessProfile $businessProfile)
    {
        $qb = $this->createQueryBuilder('bg');

        $images = $qb->where('bg.businessProfile = :businessProfile')
            ->andWhere('bg.type = :businessProfilePhotoImageType')
            ->andWhere('bg.isActive = true')
            ->setParameter('businessProfile', $businessProfile)
            ->setParameter('businessProfilePhotoImageType', OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * @param $ids
     * @return array
     */
    public function findBusinessGalleriesByIdsArray($ids)
    {
        $queryBuilder = $this->createQueryBuilder('bg')
            ->where('bg.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $queryBuilder->getQuery()->getResult();
    }
}
