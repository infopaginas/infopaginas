<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PaymentMethod
 *
 * @ORM\Table(name="payment_method")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\PaymentMethodRepository")
 * @UniqueEntity("name")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation")
 */
class PaymentMethod implements
    DefaultEntityInterface,
    CopyableEntityInterface,
    OxaPersonalTranslatableInterface,
    ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use ChangeStateTrait;

    const PAYMENT_METHOD_FIELD_NAME = 'name';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Related to PAYMENT_METHOD_NAME const
     * @var string - Payment method name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="paymentMethods",
     *     cascade={"persist"}
     *     )
     */
    protected $businessProfiles;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusiness",
     *     mappedBy="paymentMethods",
     *     cascade={"persist"}
     *     )
     */
    protected $emergencyBusinesses;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\EmergencyBundle\Entity\EmergencyDraftBusiness",
     *     mappedBy="paymentMethods",
     *     cascade={"persist"}
     *     )
     */
    protected $emergencyDraftBusinesses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var string - Payment method name
     *
     * @ORM\Column(name="type", type="string", length=100, nullable=true)
     */
    protected $type;

    /**
     * @var Media - Media
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="paymentMethod",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $image;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfiles     = new ArrayCollection();
        $this->emergencyBusinesses  = new ArrayCollection();
        $this->emergencyDraftBusinesses = new ArrayCollection();
        $this->translations         = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * @return string
     */
    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PaymentMethod
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param Media $image
     *
     * @return PaymentMethod
     */
    public function setImage(Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return PaymentMethod
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles[] = $businessProfile;
        $businessProfile->addPaymentMethod($this);

        return $this;
    }

    /**
     * Remove businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles->removeElement($businessProfile);
        $businessProfile->removePaymentMethod($this);
    }

    /**
     * Get businessProfiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessProfiles()
    {
        return $this->businessProfiles;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\EmergencyBundle\Entity\EmergencyBusiness $business
     *
     * @return PaymentMethod
     */
    public function addEmergencyBusiness(\Domain\EmergencyBundle\Entity\EmergencyBusiness $business)
    {
        $this->emergencyBusinesses->add($business);
        $business->addPaymentMethod($this);

        return $this;
    }

    /**
     * Remove $business
     *
     * @param \Domain\EmergencyBundle\Entity\EmergencyBusiness $business
     */
    public function removeEmergencyBusiness(\Domain\EmergencyBundle\Entity\EmergencyBusiness $business)
    {
        $this->emergencyBusinesses->removeElement($business);
        $business->removePaymentMethod($this);
    }

    /**
     * Get emergencyBusinesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmergencyBusinesses()
    {
        return $this->emergencyBusinesses;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\EmergencyBundle\Entity\EmergencyDraftBusiness $business
     *
     * @return PaymentMethod
     */
    public function addEmergencyDraftBusiness(\Domain\EmergencyBundle\Entity\EmergencyDraftBusiness $business)
    {
        $this->emergencyDraftBusinesses->add($business);
        $business->addPaymentMethod($this);

        return $this;
    }

    /**
     * Remove $business
     *
     * @param \Domain\EmergencyBundle\Entity\EmergencyDraftBusiness $business
     */
    public function removeEmergencyDraftBusiness(\Domain\EmergencyBundle\Entity\EmergencyDraftBusiness $business)
    {
        $this->emergencyDraftBusinesses->removeElement($business);
        $business->removePaymentMethod($this);
    }

    /**
     * Get emergencyDraftBusinesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmergencyDraftBusinesses()
    {
        return $this->emergencyDraftBusinesses;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return PaymentMethod
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public static function getTranslatableFields()
    {
        return [
            self::PAYMENT_METHOD_FIELD_NAME
        ];
    }

    public function getTranslationClass(): string
    {
        return PaymentMethodTranslation::class;
    }
}
