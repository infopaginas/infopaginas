<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

/**
 * BusinessProfilePhone
 *
 * @ORM\Table(name="business_profile_phone")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfilePhoneRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BusinessProfilePhone
{
    const REGEX_PHONE_PATTERN = '/^\d{3}-\d{3}-\d{4}$/';
    const MAX_PHONE_LENGTH = 15;

    const PHONE_TYPE_MAIN       = 'main';
    const PHONE_TYPE_SECONDARY  = 'secondary';
    const PHONE_TYPE_FAX        = 'fax';

    const PHONE_PRIORITY_MAIN       = 10;
    const PHONE_PRIORITY_SECONDARY  = 30;
    const PHONE_PRIORITY_FAX        = 20;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Related const MAX_PHONE_LENGTH
     * @var string - Contact phone number
     *
     * @ORM\Column(name="phone", type="string", length=15)
     * @Assert\NotBlank()
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10,
     *      options={"default": BusinessProfilePhone::PHONE_TYPE_SECONDARY})
     * @Assert\Choice(callback = "getTypesAssert", multiple = false)
     */
    protected $type;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", options={"default": BusinessProfilePhone::PHONE_PRIORITY_SECONDARY})
     */
    protected $priority;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="phones",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    protected $changeState;

    public function __construct()
    {
        $this->type     = self::PHONE_TYPE_MAIN;
        $this->priority = self::PHONE_PRIORITY_MAIN;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s] %s', $this->getType(), $this->getPhone());
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        $data = [
            'type'  => $this->getType(),
            'value' => $this->getPhone(),
        ];

        return json_encode($data);
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
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return BusinessProfilePhone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return BusinessProfilePhone
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public static function getTypesAssert()
    {
        return array_keys(self::getTypes());
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return BusinessProfilePhone
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::PHONE_TYPE_MAIN       => 'business_profile_phone.type.main',
            self::PHONE_TYPE_SECONDARY  => 'business_profile_phone.type.secondary',
            self::PHONE_TYPE_FAX        => 'business_profile_phone.type.fax',
        ];
    }

    /**
     * @return array
     */
    public static function getTypePriorities()
    {
        return [
            self::PHONE_TYPE_MAIN       => self::PHONE_PRIORITY_MAIN,
            self::PHONE_TYPE_SECONDARY  => self::PHONE_PRIORITY_SECONDARY,
            self::PHONE_TYPE_FAX        => self::PHONE_PRIORITY_FAX,
        ];
    }

    /**
     * @return array
     */
    public static function getTypeIcons()
    {
        return [
            self::PHONE_TYPE_MAIN       => 'fa-phone',
            self::PHONE_TYPE_SECONDARY  => 'fa-phone',
            self::PHONE_TYPE_FAX        => 'fa-fax',
        ];
    }

    /**
     * @param string $type
     *
     * @return int
     */
    public static function getPriorityByType($type)
    {
        $priorities = self::getTypePriorities();

        if (!array_key_exists($type, $priorities)) {
            $type = self::PHONE_TYPE_SECONDARY;
        }

        return $priorities[$type];
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return BusinessProfilePhone
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setPriorityValue()
    {
        $this->priority = self::getPriorityByType($this->type);
    }

    public function getChangeState()
    {
        return $this->changeState;
    }

    public function setChangeState(array $changeState) : self
    {
        $this->changeState = $changeState;

        return $this;
    }
}
