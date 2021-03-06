<?php

namespace Domain\BusinessBundle\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\LandingPageShortCutSearch;
use Domain\BusinessBundle\Entity\Translation\Media\BusinessGalleryTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Gallery;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Entity\BaseGalleryHasMedia;
use Sonata\MediaBundle\Model\MediaInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessGallery
 *
 * @ORM\Table(name="business_gallery")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessGalleryRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\Media\BusinessGalleryTranslation")
 */
class BusinessGallery implements DefaultEntityInterface, OxaPersonalTranslatableInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use ChangeStateTrait;

    const MAX_IMAGES_PER_BUSINESS = 25;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Description of Image
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="images"
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var \Oxa\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="businessGallery",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     * @Assert\NotBlank()
     */
    protected $media;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\Media\BusinessGalleryTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var string - Slogan of a Business
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId() ? sprintf('%s: %s', $this->getId(), $this->getBusinessProfile()->__toString()) : '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->type = OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES;
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return BusinessGallery
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
     * @return BusinessGallery
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
     * @return BusinessGallery
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
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return BusinessGallery
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @param mixed $data
     * @param Media $media
     *
     * @return BusinessGallery
     */
    public static function createFromChangeSet($data, Media $media)
    {
        $gallery = new BusinessGallery();
        $gallery->setDescription($data->description);
        $gallery->setType($data->type);
        $gallery->setMedia($media);

        return $gallery;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return LandingPageShortCutSearch
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

    public function getTranslationClass(): string
    {
        return BusinessGalleryTranslation::class;
    }
}
