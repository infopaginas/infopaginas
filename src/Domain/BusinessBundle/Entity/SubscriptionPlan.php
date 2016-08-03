<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SubscriptionPlan
 *
 * @ORM\Table(name="subscription_plan")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\SubscriptionPlanRepository")
 * @UniqueEntity("code")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\SubscriptionPlanTranslation")
 */
class SubscriptionPlan implements DefaultEntityInterface, SubscriptionPlanInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string - Subscription name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string - Subscription code
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    protected $code;

    /**
     * Influence on search result, bigger number - more relevant business
     * @var string - Subscription rank
     *
     * @ORM\Column(name="rank", type="integer", nullable=false, options={"default":0})
     */
    protected $rank = 0;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Subscription",
     *     mappedBy="subscriptionPlan",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $subscriptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\SubscriptionPlanTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @return array
     */
    public static function getCodes()
    {
        return [
            self::CODE_FREE,
            self::CODE_PRIORITY,
            self::CODE_PREMIUM_PLUS,
            self::CODE_PREMIUM_GOLD,
            self::CODE_PREMIUM_PLATINUM
        ];
    }

    /**
     * @return array
     */
    public static function getCodeValues()
    {
        return [
            self::CODE_FREE             => 'free',
            self::CODE_PRIORITY         => 'priority',
            self::CODE_PREMIUM_PLUS     => 'premium_plus',
            self::CODE_PREMIUM_GOLD     => 'premium_gold',
            self::CODE_PREMIUM_PLATINUM => 'premium_platinum'
        ];
    }

    /**
     * @return array
     */
    public static function getCodeNames()
    {
        return [
            self::CODE_FREE             => 'Free',
            self::CODE_PRIORITY         => 'Priority',
            self::CODE_PREMIUM_PLUS     => 'Premium Plus',
            self::CODE_PREMIUM_GOLD     => 'Premium Gold',
            self::CODE_PREMIUM_PLATINUM => 'Premium Platinum'
        ];
    }

    /**
     * @return string
     */
    public function getCodeValue()
    {
        $codes = self::getCodeValues();

        return $codes[$this->getCode()];
    }

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
        $this->subscriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        switch (true) {
            case $this->getName():
                $result = $this->getName();
                break;
            case $this->getId():
                $result = sprintf('id(%s): not translated', $this->getId());
                break;
            default:
                $result = 'New subscription plan';
        }
        return $result;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Subscription
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
     * Set code
     *
     * @param integer $code
     *
     * @return Subscription
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
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

    /**
     * Add subscription
     *
     * @param \Domain\BusinessBundle\Entity\Subscription $subscription
     *
     * @return SubscriptionPlan
     */
    public function addSubscription(\Domain\BusinessBundle\Entity\Subscription $subscription)
    {
        $this->subscriptions[] = $subscription;

        return $this;
    }

    /**
     * Remove subscription
     *
     * @param \Domain\BusinessBundle\Entity\Subscription $subscription
     */
    public function removeSubscription(\Domain\BusinessBundle\Entity\Subscription $subscription)
    {
        $this->subscriptions->removeElement($subscription);
    }

    /**
     * Get subscriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     *
     * @return SubscriptionPlan
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }
}
