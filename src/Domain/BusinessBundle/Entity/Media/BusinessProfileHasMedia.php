<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/21/16
 * Time: 4:57 PM
 */

namespace Domain\BusinessBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\MediaBundle\Entity\BaseGalleryHasMedia;
use Sonata\MediaBundle\Model\MediaInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessProfileHasMedia
 *
 * @ORM\Table(name="business_profile_has_media")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class BusinessProfileHasMedia implements DefaultEntityInterface
{
    use DefaultEntityTrait;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile", 
     *     cascade={"persist"},
     *     inversedBy="images"
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id")
     */
    protected $businessProfile;

    /**
     * @var \Oxa\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media", 
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return ($this->getId()) ? strval($this->getId()) : 'New BusinessMedia';
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return BusinessProfileHasMedia
     */
    public function setBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * Set media
     *
     * @param MediaInterface $media
     *
     * @return BusinessProfileHasMedia
     */
    public function setMedia(MediaInterface $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Oxa\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
