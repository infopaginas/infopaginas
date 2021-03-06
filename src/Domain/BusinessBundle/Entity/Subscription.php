<?php

namespace Domain\BusinessBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Util\Traits\DatetimePeriodStatusTrait;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\SubscriptionRepository")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\SubscriptionTranslation")
 */
class Subscription implements
    DefaultEntityInterface,
    OxaPersonalTranslatableInterface,
    DatetimePeriodStatusInterface,
    ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use DatetimePeriodStatusTrait;
    use ChangeStateTrait;

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
        $this->startDate = new DateTime('yesterday');
        $this->endDate = new DateTime('yesterday +1 year');
    }

    /**
     * @return string
     */
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

    public function __clone()
    {
        $this->id = null;
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

    public function getTranslationClass(): string
    {
        return SubscriptionTranslation::class;
    }
}
