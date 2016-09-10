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
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
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
    const CANT_CREATE_TEMP_FILE_ERROR_MESSAGE = 'File can not be opened.';

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
            $media = $this->createNewMediaEntryFromUploadedFile($file);
            array_push($images, $media);
        }

        $this->getEntityManager()->flush();

        foreach ($images as $image) {
            $businessProfile = $this->addNewItemToBusinessProfileGallery($businessProfile, $image);
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $url
     * @return BusinessProfile
     * @throws \Exception
     */
    public function createNewEntryFromRemoteFile(BusinessProfile $businessProfile, string $url)
    {
        if (SiteHelper::checkUrlExistence($url) && getimagesize($url)) {
            $file = tmpfile();

            if ($file === false) {
                throw new \Exception(self::CANT_CREATE_TEMP_FILE_ERROR_MESSAGE);
            }

            // Put content in this file
            $path = stream_get_meta_data($file)['uri'];
            file_put_contents($path, file_get_contents($url));

            // the UploadedFile of the user image
            // referencing the temp file (used for validation only)
            $uploadedFile = new UploadedFile($path, $path, null, null, null, true);

            $media = $this->createNewMediaEntryFromUploadedFile($uploadedFile);

            $businessProfile = $this->addNewItemToBusinessProfileGallery($businessProfile, $media);

            return $businessProfile;
        } else {
            return false;
        }
    }

    /**
     * @param UploadedFile $file
     * @return Media
     */
    public function createNewMediaEntryFromUploadedFile(UploadedFile $file) : Media
    {
        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext(OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES);
        $media->setProviderName(OxaMediaInterface::PROVIDER_IMAGE);

        $this->getSonataMediaManager()->save($media, false);

        return $media;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param Media $media
     * @return BusinessProfile
     */
    public function addNewItemToBusinessProfileGallery(BusinessProfile $businessProfile, Media $media) : BusinessProfile
    {
        $businessGallery = new BusinessGallery();
        $businessGallery->setMedia($media);
        $businessProfile->addImage($businessGallery);

        return $businessProfile;
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
