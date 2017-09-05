<?php

namespace Domain\BusinessBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Model\DataType\MediaDTO;
use Domain\BusinessBundle\Repository\BusinessGalleryRepository;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\DataTransferObjectInterface;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\DTOAssemblerInterface;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Repository\MediaRepository;

/**
 * Class MediaDTOAssembler
 * @package Domain\BusinessBundle\Model
 */
class MediaDTOAssembler implements DTOAssemblerInterface
{
    const BUSINESS_PROFILES_ARRAY_KEY = 'business_profiles';
    const BUSINESS_GALLERIES_ARRAY_KEY = 'business_galleries';

    /** @var EntityManager $entityManager */
    protected $entityManager;

    /**
     * MediaDTOAssembler constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Media $media
     * @return DataTransferObjectInterface
     */
    public function createDTO($media) : DataTransferObjectInterface
    {
        $data = [
            'id'  => $media->getId(),
            'url' => $media->getUrl(),
            self::BUSINESS_PROFILES_ARRAY_KEY  => $this->unpackCollectionIds(
                $media->getBusinessProfiles()->toArray()
            ),
            self::BUSINESS_GALLERIES_ARRAY_KEY => $this->unpackCollectionIds(
                $media->getBusinessGallery()->toArray()
            ),
        ];

        return new MediaDTO($data);
    }

    /**
     * @param string $serialized
     * @return ArrayCollection
     */
    public function createDO(string $serialized) : ArrayCollection
    {
        $mediasList = MediaDTO::deserialize($serialized);

        $medias = new ArrayCollection();

        foreach ($mediasList as $mediaData) {
            $media = $mediaData['id'] ? $this->getMediaRepository()->find($mediaData['id']) : new Media();
            $media->setUrl($mediaData['url']);
            $this->addBusinessProfilesToMedia($media, $mediaData[self::BUSINESS_PROFILES_ARRAY_KEY]);
            $this->addGalleriesToMedia($media, $mediaData[self::BUSINESS_GALLERIES_ARRAY_KEY]);

            $medias->add($media);
        }

        return $medias;
    }

    /**
     * @param Media $media
     * @param array $profilesData
     */
    protected function addBusinessProfilesToMedia(Media &$media, array $profilesData)
    {
        $businessProfilesIds = $this->unpackCollectionIds($profilesData);

        $businessProfiles = $this->getBusinessProfileRepository()->findBusinessProfilesByIdsArray($businessProfilesIds);

        foreach ($businessProfiles as $businessProfile) {
            $media->addBusinessProfile($businessProfile);
        }
    }

    /**
     * @param Media $media
     * @param array $galleriesData
     */
    protected function addGalleriesToMedia(Media &$media, array $galleriesData)
    {
        $galleriesIds = $this->unpackCollectionIds($galleriesData);

        $businessGalleries = $this->getBusinessGalleryRepository()->findBusinessGalleriesByIdsArray($galleriesIds);

        foreach ($businessGalleries as $gallery) {
            $media->addBusinessGallery($gallery);
        }
    }

    /**
     * @param array $collectionItems
     * @return array
     */
    protected function unpackCollectionIds(array $collectionItems) : array
    {
        $ids = array_map(function ($item) {
            return $item->getId();
        }, $collectionItems);

        return $ids;
    }

    /**
     * @return MediaRepository
     */
    protected function getMediaRepository() : MediaRepository
    {
        return $this->getEntityManager()->getRepository(Media::class);
    }

    /**
     * @return BusinessProfileRepository
     */
    protected function getBusinessProfileRepository() : BusinessProfileRepository
    {
        return $this->getEntityManager()->getRepository(BusinessProfile::class);
    }

    /**
     * @return BusinessGalleryRepository
     */
    protected function getBusinessGalleryRepository() : BusinessGalleryRepository
    {
        return $this->getEntityManager()->getRepository(BusinessGallery::class);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
}
