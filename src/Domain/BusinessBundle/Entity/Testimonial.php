<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\TestimonialTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;

/**
 * @ORM\Table(name="testimonial")
 * @ORM\Entity()
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\TestimonialTranslation")
 */
class Testimonial implements DefaultEntityInterface, ChangeStateInterface, OxaPersonalTranslatableInterface
{
    use DefaultEntityTrait;
    use ChangeStateTrait;
    use PersonalTranslatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    protected $title;

    /**
     * @var BusinessProfile
     *
     * @ORM\ManyToOne(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="testimonials"
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", nullable=false)
     */
    protected $businessProfile;

    /**
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="string", length=1000)
     * @Assert\Length(max=1000)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @var Media - Media Image
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="coupons",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $image;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\TestimonialTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Testimonial
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return Testimonial
     */
    public function setBusinessProfile(BusinessProfile $businessProfile)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Testimonial
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Media $image
     * @return Testimonial
     */
    public function setImage(Media $image)
    {
        $this->image = $image;

        return $this;
    }

    public function getTranslationClass(): string
    {
        return TestimonialTranslation::class;
    }
}