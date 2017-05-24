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
 */
class BusinessProfilePhone
{
    const REGEX_PHONE_PATTERN = '/^\d([-]*\d){0,10}$/';
    const MAX_PHONE_LENGTH = 15;

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
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="phones",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    public function __toString()
    {
        return $this->getPhone() ?: '';
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
     * @return BusinessProfilePhone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
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
}
