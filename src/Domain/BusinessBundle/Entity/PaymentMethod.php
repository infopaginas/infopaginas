<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
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
class PaymentMethod implements DefaultEntityInterface, CopyableEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    /* const value related to icon name */
    const PAYMENT_METHOD_TYPE_CASH      = 'cash';
    const PAYMENT_METHOD_TYPE_CHECK     = 'check';
    const PAYMENT_METHOD_TYPE_PAYPAL    = 'paypal';
    const PAYMENT_METHOD_TYPE_ATH_MOVIL = 'ath_movil';
    const PAYMENT_METHOD_TYPE_ONLINE    = 'online';
    const PAYMENT_METHOD_TYPE_DEBIT     = 'debit';

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
        $this->businessProfiles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?: '';
    }

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
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation $translation)
    {
        $this->translations->removeElement($translation);
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

    public static function getTranslatableFields()
    {
        return [
            self::PAYMENT_METHOD_FIELD_NAME
        ];
    }

    public static function getRequiredPaymentMethods()
    {
        return [
            self::PAYMENT_METHOD_TYPE_CASH,
            self::PAYMENT_METHOD_TYPE_CHECK,
            self::PAYMENT_METHOD_TYPE_PAYPAL,
            self::PAYMENT_METHOD_TYPE_ATH_MOVIL,
            self::PAYMENT_METHOD_TYPE_ONLINE,
            self::PAYMENT_METHOD_TYPE_DEBIT,
        ];
    }

    /**
     * @return array
     */
    public static function getPaymentMethodData()
    {
        return [
            self::PAYMENT_METHOD_TYPE_CASH => [
                'nameEn' => 'Cash',
                'nameEs' => 'Efectivo',
                'type' => self::PAYMENT_METHOD_TYPE_CASH,
            ],
            self::PAYMENT_METHOD_TYPE_CHECK => [
                'nameEn' => 'Check',
                'nameEs' => 'Cheque',
                'type' => self::PAYMENT_METHOD_TYPE_CHECK,
            ],
            self::PAYMENT_METHOD_TYPE_PAYPAL => [
                'nameEn' => 'PayPal',
                'nameEs' => 'PayPal',
                'type' => self::PAYMENT_METHOD_TYPE_PAYPAL,
            ],
            self::PAYMENT_METHOD_TYPE_ATH_MOVIL => [
                'nameEn' => 'ATHMovil',
                'nameEs' => 'ATHMovil',
                'type' => self::PAYMENT_METHOD_TYPE_ATH_MOVIL,
            ],
            self::PAYMENT_METHOD_TYPE_ONLINE => [
                'nameEn' => 'Online Payment',
                'nameEs' => 'Online Payment',
                'type' => self::PAYMENT_METHOD_TYPE_ONLINE,
            ],
            self::PAYMENT_METHOD_TYPE_DEBIT => [
                'nameEn' => 'Debit Card',
                'nameEs' => 'Debito',
                'type' => self::PAYMENT_METHOD_TYPE_DEBIT,
            ],
        ];
    }
}
