<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 17.07.16
 * Time: 16:09
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

/**
 * Class BusinessGalleryManager
 * @package Domain\BusinessBundle\Manager
 */
class BusinessGalleryManager
{
    /** @var EntityManager */
    private $entityManager;

    /** @var MediaManager */
    private $mediaManager;

    /**
     * BusinessGalleryManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, MediaManager $mediaManager)
    {
        $this->entityManager = $entityManager;
        $this->mediaManager  = $mediaManager;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param FileBag $fileBag
     * @return BusinessProfile
     */
    public function fillBusinessGallery(BusinessProfile $businessProfile, FileBag $fileBag)
    {
        $images = [];

        /** @var UploadedFile $file */
        foreach ($fileBag->get('files') as $file) {
            $media = new Media();
            $media->setBinaryContent($file);
            $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES);
            $media->setProviderName(OxaMediaInterface::PROVIDER_IMAGE);

            $this->getSonataMediaManager()->save($media, false);

            array_push($images, $media);
        }

        $this->getEntityManager()->flush();

        foreach ($images as $image) {
            $businessGallery = new BusinessGallery();
            $businessGallery->setMedia($image);

            $businessProfile->addImage($businessGallery);
        }

        return $businessProfile;
    }

    /**
     * Used to restore images in case then task rejected
     *
     * @access public
     * @param BusinessProfile $businessProfile
     */
    public function restoreBusinessProfileImages(BusinessProfile $businessProfile)
    {
        $actualBusinessProfile = $businessProfile->getActualBusinessProfile();

        $images = $this->getRepository()->findBusinessProfileRemovedImages($actualBusinessProfile);

        foreach ($images as $image) {
            $image->setDeletedAt(null);
            $this->getEntityManager()->persist($image);
        }

        foreach ($businessProfile->getImages() as $image) {
            $actualBusinessProfile->addImage($image);
            $this->getEntityManager()->persist($actualBusinessProfile);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Go through business profile images and setup profile logo, if found
     *
     * @access public
     * @param BusinessProfile $businessProfile
     */
    public function setupBusinessProfileLogo(BusinessProfile $businessProfile)
    {
        foreach ($businessProfile->getImages() as $image) {
            if ($image->getType() === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
                $businessProfile->setLogo($image->getMedia());
                $this->getEntityManager()->persist($businessProfile);
            }
        }
    }

    /**
     * @return \Sonata\MediaBundle\Entity\MediaManager
     */
    private function getSonataMediaManager() : MediaManager
    {
        return $this->mediaManager;
    }

    /**
     * @return BusinessGalleryRepository
     */
    private function getRepository() : BusinessGalleryRepository
    {
        return $this->getEntityManager()->getRepository('DomainBusinessBundle:Media\BusinessGallery');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }
}
