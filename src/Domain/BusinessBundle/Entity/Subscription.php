<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\SubscriptionInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\SubscriptionRepository")
 * @UniqueEntity("code")
 */
class Subscription implements DefaultEntityInterface, SubscriptionInterface
{
    use DefaultEntityTrait;
    
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var string - Subscription code
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    protected $code;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="subscription",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $businessProfiles;

    /**
     * @return array
     */
    public static function getCodes()
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
     * @return array
     */
    public function getCodeValue()
    {
        $codes = self::getCodes();

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
        $this->businessProfiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return ($this->getName()) ?: 'New subscription';
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
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Subscription
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles[] = $businessProfile;

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
}
