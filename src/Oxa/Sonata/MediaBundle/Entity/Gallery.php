<?php

namespace Oxa\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;

/**
 * @ORM\Table(name="media__gallery")
 * @ORM\Entity()
 */
class Gallery extends BaseGallery
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add galleryHasMedia
     *
     * @param GalleryHasMediaInterface $galleryHasMedia
     *
     * @return Gallery
     */
    public function addGalleryHasMedia(GalleryHasMediaInterface $galleryHasMedia)
    {
        $this->galleryHasMedias[] = $galleryHasMedia;

        return $this;
    }

    /**
     * Remove galleryHasMedia
     *
     * @param GalleryHasMediaInterface $galleryHasMedia
     */
    public function removeGalleryHasMedia(GalleryHasMediaInterface $galleryHasMedia)
    {
        $this->galleryHasMedias->removeElement($galleryHasMedia);
    }
}
