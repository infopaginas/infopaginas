<?php

namespace Domain\BusinessBundle\Repository;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;

/**
 * Class BusinessGalleryRepository
 * @package Domain\BusinessBundle\Repository
 */
class BusinessGalleryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param BusinessProfile $businessProfile
     * @param string $locale
     *
     * @return array
     */
    public function findBusinessProfileAdvertisementImages(BusinessProfile $businessProfile, $locale)
    {
        $qb = $this->createQueryBuilder('bg');

        $qb->where('bg.businessProfile = :businessProfile')
            ->andWhere('bg.type = :businessProfileAdvertisementImageType')
            ->andWhere('bg.isActive = true')
            ->setParameter('businessProfile', $businessProfile)
            ->setParameter('businessProfileAdvertisementImageType', OxaMediaInterface::CONTEXT_BANNER)
        ;

        $query = $qb->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $locale
     *
     * @return array
     */
    public function findBusinessProfilePhotoImages(BusinessProfile $businessProfile, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $qb = $this->createQueryBuilder('bg');

        $qb->where('bg.businessProfile = :businessProfile')
            ->andWhere('bg.type = :businessProfilePhotoImageType')
            ->andWhere('bg.isActive = true')
            ->setParameter('businessProfile', $businessProfile)
            ->setParameter('businessProfilePhotoImageType', OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES)
        ;

        $query = $qb->getQuery();

        if ($locale) {
            SiteHelper::setLocaleQueryHint($query, $locale);
        }

        return $query->getResult();
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
