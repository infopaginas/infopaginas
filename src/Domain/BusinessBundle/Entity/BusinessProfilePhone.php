<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Domain\BusinessBundle\Form\Handler\BusinessFormHandlerInterface;

/**
 * BusinessProfilePhone
 *
 * @ORM\Table(name="business_profile_phone")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfilePhoneRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"phone"}, groups={BusinessFormHandlerInterface::UNIQUE_PHONE_VALIDATION_GROUP})
 */
class BusinessProfilePhone implements ChangeStateInterface
{
    use ChangeStateTrait;

    public const REGEX_PHONE_PATTERN = '/^\d{3}-\d{3}-\d{4}$|' .
                                '^\(\d{3}\)\s\d{3}\s\d{4}$|' .
                                '^\(\d{3}\)-\d{3}-\d{4}$|' .
                                '^\(\d{3}\)\d{3}-\d{4}$|' .
                                '^\(\d{3}\)\s\d{3}-\d{4}$|' .
                                '^\d{10}$|' .
                                '^\d{3}\s\d{3}\s\d{4}$/';

    public const MAX_PHONE_LENGTH     = 15;
    public const MAX_EXTENSION_LENGTH = 6;
    public const MIN_EXTENSION_LENGTH = 1;

    public const PHONE_TYPE_MAIN      = 'main';
    public const PHONE_TYPE_SECONDARY = 'secondary';
    public const PHONE_TYPE_FAX       = 'fax';

    public const PHONE_PRIORITY_MAIN      = 10;
    public const PHONE_PRIORITY_SECONDARY = 30;
    public const PHONE_PRIORITY_FAX       = 20;

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
     * @Assert\Length(max=BusinessProfilePhone::MAX_PHONE_LENGTH)
     */
    private $phone;

    /**
     * @var string - Phone extension
     *
     * @ORM\Column(name="extension", type="string", length=6, nullable=true)
     * @Assert\Length(min=BusinessProfilePhone::MIN_EXTENSION_LENGTH, max=BusinessProfilePhone::MAX_EXTENSION_LENGTH)
     * @Assert\Type("digit")
     */
    private $extension;

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
        return array_values(self::getTypes());
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
            'business_profile_phone.type.main'      => self::PHONE_TYPE_MAIN,
            'business_profile_phone.type.secondary' => self::PHONE_TYPE_SECONDARY,
            'business_profile_phone.type.fax'       => self::PHONE_TYPE_FAX,
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

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     * @return BusinessProfilePhone
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }
}
