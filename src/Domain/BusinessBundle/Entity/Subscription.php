<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Util\Traits\DatetimePeriodStatusTrait;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Model\DatetimePeriodInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DatetimePeriodTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\SubscriptionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation")
 */
class Subscription implements DefaultEntityInterface, TranslatableInterface, DatetimePeriodStatusInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use DatetimePeriodStatusTrait;

    const PROPERTY_NAME_UPDATED_AT = 'updatedAt';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="subscriptions",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\SubscriptionPlan",
     *     inversedBy="subscriptions",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="subscription_plan_id", referencedColumnName="id", nullable=false)
     */
    protected $subscriptionPlan;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation",
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        if ($this->getId()) {
            $result = sprintf(
                '%s: %s (%s - %s)',
                $this->getId(),
                $this->getSubscriptionPlan(),
                $this->getStartDate()->format('d/M/Y, H:m'),
                $this->getEndDate()->format('d/M/Y, H:m')
            );
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Subscription
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
     * Set subscriptionPlan
     *
     * @param \Domain\BusinessBundle\Entity\SubscriptionPlan $subscriptionPlan
     *
     * @return Subscription
     */
    public function setSubscriptionPlan(\Domain\BusinessBundle\Entity\SubscriptionPlan $subscriptionPlan = null)
    {
        $this->subscriptionPlan = $subscriptionPlan;

        return $this;
    }

    /**
     * Get subscriptionPlan
     *
     * @return \Domain\BusinessBundle\Entity\SubscriptionPlan
     */
    public function getSubscriptionPlan()
    {
        return $this->subscriptionPlan;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
