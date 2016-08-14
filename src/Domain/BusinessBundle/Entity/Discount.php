<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Util\Traits\DatetimePeriodStatusTrait;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DatetimePeriodInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DatetimePeriodTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Discount
 *
 * @ORM\Table(name="discount")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\DiscountRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\DiscountTranslation")
 */
class Discount implements DefaultEntityInterface, TranslatableInterface, DatetimePeriodStatusInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use DatetimePeriodStatusTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Discount title
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var string - Discount value
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="value", type="float")
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Coupon",
     *     inversedBy="discounts",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="coupon_id", referencedColumnName="id")
     */
    protected $coupon;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="discounts",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", nullable=false)
     */
    protected $businessProfile;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\DiscountTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->getId() ? sprintf('%s: %s', $this->getId(), $this->getCoupon()) : 'New Discount';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Discount
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
     * Set value
     *
     * @param float $value
     *
     * @return Discount
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set coupon
     *
     * @param \Domain\BusinessBundle\Entity\Coupon $coupon
     *
     * @return Discount
     */
    public function setCoupon(\Domain\BusinessBundle\Entity\Coupon $coupon = null)
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Get coupon
     *
     * @return \Domain\BusinessBundle\Entity\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\DiscountTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\DiscountTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Discount
     */
    public function setBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
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
}
