<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oxa\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @param \Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia
     *
     * @return Gallery
     */
    public function addGalleryHasMedia(\Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia)
    {
        $this->galleryHasMedias[] = $galleryHasMedia;

        return $this;
    }

    /**
     * Remove galleryHasMedia
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia
     */
    public function removeGalleryHasMedia(\Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia)
    {
        $this->galleryHasMedias->removeElement($galleryHasMedia);
    }
}
