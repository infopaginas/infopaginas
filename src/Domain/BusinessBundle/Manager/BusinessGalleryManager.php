<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\VideoBundle\Entity\VideoMedia;
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
     * @param string          $context
     * @param FileBag         $fileBag
     *
     * @return BusinessProfile
     */
    public function fillBusinessGallery(BusinessProfile $businessProfile, $context, FileBag $fileBag)
    {
        $images = [];

        /** @var UploadedFile $file */
        foreach ($fileBag->get('files') as $file) {
            if ($this->checkUploadedFileSize($file->getSize(), $context) and $this->checkUploadedFileType($file)) {
                $media = $this->createNewMediaEntryFromUploadedFile(
                    $file,
                    $context
                );
                array_push($images, $media);
            }
        }

        $this->getEntityManager()->flush();

        foreach ($images as $image) {
            $businessProfile = $this->addNewItemToBusinessProfileGallery($businessProfile, $image);
        }

        return $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string          $context
     * @param string          $url
     *
     * @return array
     * @throws \Exception
     */
    public function createNewEntryFromRemoteFile(BusinessProfile $businessProfile, string $context, string $url)
    {
        $headers = SiteHelper::checkUrlExistence($url);
        $error = [];

        if ($headers and in_array($headers['content_type'], SiteHelper::$imageContentTypes) and exif_imagetype($url)) {
            if ($this->checkImageSize($headers, $context)) {
                $file = tmpfile();

                if ($file === false) {
                    throw new \Exception(self::CANT_CREATE_TEMP_FILE_ERROR_MESSAGE);
                }

                $ext = pathinfo($url, PATHINFO_EXTENSION);
                // Put content in this file
                $path = stream_get_meta_data($file)['uri'] . uniqid() . '.' . $ext  ;
                file_put_contents($path, file_get_contents($url));

                // the UploadedFile of the user image
                // referencing the temp file (used for validation only)
                $uploadedFile = new UploadedFile($path, $path, null, null, null, true);

                $media = $this->createNewMediaEntryFromUploadedFile($uploadedFile, $context);

                $this->getEntityManager()->flush();

                $this->addNewItemToBusinessProfileGallery($businessProfile, $media);

                return $error;
            } else {
                $error = [
                    'message' => 'business_profile.images.invalid_size',
                    'params'  => [
                        '{{ limit }}' => Media::getSizeLimitMessage(Media::getMediaMaxSizeByContext($context)),
                    ],
                ];
            }
        } else {
            $error = [
                'message' => 'business_profile.images.invalid_url',
                'params'  => [],
            ];
        }

        return $error;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string          $context
     * @param string          $url
     *
     * @return BusinessProfile|bool
     * @throws \Exception
     */
    public function createNewEntryFromLocalFile(BusinessProfile $businessProfile, string $context, string $url)
    {
        $isExist = file_exists($url);

        if ($isExist && in_array(mime_content_type($url), SiteHelper::$imageContentTypes) && exif_imagetype($url)) {
            $uploadedFile = new UploadedFile($url, $url, null, null, null, true);

            $media = $this->createNewMediaEntryFromUploadedFile($uploadedFile, $context);

            $businessProfile = $this->addNewItemToBusinessProfileGallery($businessProfile, $media);

            return $businessProfile;
        } else {
            return false;
        }
    }

    /**
     * @param VideoMedia $video
     * @param string     $path
     * @param bool       $removeOldPoster
     *
     * @return VideoMedia|null
     * @throws \Exception
     */
    public function createNewPosterFromLocalFile(VideoMedia $video, string $path, bool $removeOldPoster = false)
    {
        $isExist = file_exists($path);

        if ($isExist && in_array(mime_content_type($path), SiteHelper::$imageContentTypes) && exif_imagetype($path)) {
            $uploadedFile = new UploadedFile($path, $path, null, null, null, true);

            if ($removeOldPoster and $video->getPoster()) {
                $this->entityManager->remove($video->getPoster());
            }

            $media = $this->createNewMediaEntryFromUploadedFile($uploadedFile, OxaMediaInterface::CONTEXT_VIDEO_POSTER);

            $video->setPoster($media);

            $this->entityManager->flush();

            return $video;
        } else {
            return null;
        }
    }

    /**
     * @param UploadedFile $file
     * @param string $context
     * @return Media
     */
    public function createNewAdminMediaEntryFromUploadedFile(UploadedFile $file, $context) : Media
    {
        $media = $this->createNewMediaEntryFromUploadedFile($file, $context);

        $this->getEntityManager()->flush();

        return $media;
    }

    /**
     * @param UploadedFile $file
     * @param string $context
     * @return Media
     */
    public function createNewMediaEntryFromUploadedFile(UploadedFile $file, $context) : Media
    {
        $imageContext = $this->checkImageUploadContext($context);

        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext($imageContext);
        $media->setProviderName(OxaMediaInterface::PROVIDER_IMAGE);

        $this->getSonataMediaManager()->save($media, false);

        return $media;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param Media           $media
     *
     * @return BusinessProfile
     */
    public function addNewItemToBusinessProfileGallery(BusinessProfile $businessProfile, Media $media) : BusinessProfile
    {
        $isLogo       = $this->getContextIsOfType(
            $media->getContext(),
            OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO
        );
        $isBackground = $this->getContextIsOfType(
            $media->getContext(),
            OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND
        );

        if (!$isLogo and !$isBackground) {
            $businessGallery = new BusinessGallery();
            $businessGallery->setMedia($media);
            $businessGallery->setType($media->getContext());
            $businessProfile->addImage($businessGallery);
        }

        if ($isLogo) {
            $businessProfile->setLogo($media);
        }

        if ($isBackground) {
            $businessProfile->setBackground($media);
        }

        return $businessProfile;
    }

    /**
     * check context type
     *
     * @param string $context
     * @param string $type
     *
     * @return bool
     */
    protected function getContextIsOfType(string $context, string $type)
    {
        return ($context == $type);
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
     * Go through business profile images and setup profile logo, if found
     *
     * @access public
     * @param BusinessProfile $businessProfile
     */
    public function setupBusinessProfileBackground(BusinessProfile $businessProfile)
    {
        foreach ($businessProfile->getImages() as $image) {
            if ($image->getType() === OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND) {
                $businessProfile->setBackground($image->getMedia());
                $this->getEntityManager()->persist($businessProfile);
            }
        }
    }

    /**
     * @param string $url
     * @param string $context
     *
     * @return Media|null
     */
    public function uploadArticleImageFromRemoteFile($url, $context = Media::CONTEXT_ARTICLE)
    {
        $headers = SiteHelper::checkUrlExistence($url);

        if ($headers && in_array($headers['content_type'], SiteHelper::$imageContentTypes) && exif_imagetype($url)) {
            $file = tmpfile();

            if ($file !== false) {
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                // Put content in this file
                $path = stream_get_meta_data($file)['uri'] . uniqid() . '.' . $ext  ;
                file_put_contents($path, file_get_contents($url));

                // the UploadedFile of the user image
                // referencing the temp file (used for validation only)
                $uploadedFile = new UploadedFile($path, $path, null, null, null, true);

                $media = $this->createNewMediaEntryFromUploadedFile($uploadedFile, $context);

                $this->getEntityManager()->flush();

                return $media;
            }
        }

        return null;
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
        return $this->getEntityManager()->getRepository(BusinessGallery::class);
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param string $context
     *
     * @return string
     */
    private function checkImageUploadContext($context)
    {
        if (in_array($context, Media::getContexts())) {
            return $context;
        }

        return OxaMediaInterface::CONTEXT_DEFAULT;
    }

    /**
     * @param bool $isLogo
     *
     * @return string
     */
    private function getMigrationImageContext($isLogo)
    {
        $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES;

        if ($isLogo) {
            $context = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO;
        }

        return $context;
    }

    /**
     * @param array  $headers
     * @param string $context
     *
     * @return bool
     */
    protected function checkImageSize($headers, $context)
    {
        if (!empty($headers['download_content_length']) and $headers['download_content_length'] > 0 and
            $this->checkUploadedFileSize($headers['download_content_length'], $context)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param int     $size
     * @param string  $context
     *
     * @return bool
     */
    protected function checkUploadedFileSize($size, $context)
    {
        if (Media::getMediaMaxSizeByContext($context) >= $size) {
            return true;
        }

        return false;
    }

    /**
     * @param UploadedFile $file
     *
     * @return bool
     */
    protected function checkUploadedFileType($file)
    {
        if (in_array($file->getMimeType(), SiteHelper::$imageContentTypes)) {
            $status = true;
        } else {
            $status = false;
        }

        return $status;
    }
}
