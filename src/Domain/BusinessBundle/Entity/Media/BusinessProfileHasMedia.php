<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/21/16
 * Time: 4:57 PM
 */

namespace Domain\BusinessBundle\Entity\Media;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\MediaBundle\Entity\BaseGalleryHasMedia;
use Sonata\MediaBundle\Model\MediaInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Exception\ValidatorException;

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
     * @var string - Description of Image
     *
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    protected $position;

    /**
     * @var string - Is Primary
     *
     * @ORM\Column(name="is_primary", type="boolean", options={"default" : 0})
     */
    protected $isPrimary = false;

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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return BusinessProfileHasMedia
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     *
     * @return BusinessProfileHasMedia
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return boolean
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BusinessProfileHasMedia
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
