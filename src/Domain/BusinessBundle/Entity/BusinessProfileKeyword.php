<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessProfileKeyword
 *
 * @ORM\Table(name="business_profile_keyword")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileKeywordRepository")
 */
class BusinessProfileKeyword
{
    const KEYWORD_MIN_LENGTH = 2;
    const KEYWORD_MAX_LENGTH = 255;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - keyword
     *
     * @ORM\Column(name="value_en", type="string", length=255)
     * @Assert\NotBlank(message="business_profile.keywords.not_blank")
     * @Assert\Length(
     *      min="2",
     *      max="255",
     *      minMessage="business_profile.keywords.min_length",
     *      maxMessage="business_profile.keywords.max_length",
     *  )
     */
    private $valueEn;

    /**
     * @var string - keyword
     *
     * @ORM\Column(name="value_es", type="string", length=255)
     * @Assert\NotBlank(message="business_profile.keywords.not_blank")
     * @Assert\Length(
     *      min="2",
     *      max="255",
     *      minMessage="business_profile.keywords.min_length",
     *      maxMessage="business_profile.keywords.max_length",
     *  )
     */
    private $valueEs;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="keywords",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    public function __toString()
    {
        return $this->getValueEn() . ' - ' . $this->getValueEs();
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
    public function getValueEn()
    {
        return $this->valueEn;
    }

    /**
     * @param string $valueEn
     *
     * @return BusinessProfileKeyword
     */
    public function setValueEn($valueEn)
    {
        $this->valueEn = $valueEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getValueEs()
    {
        return $this->valueEs;
    }

    /**
     * @param string $valueEs
     *
     * @return BusinessProfileKeyword
     */
    public function setValueEs($valueEs)
    {
        $this->valueEs = $valueEs;

        return $this;
    }

    /**
     * Set businessProfile
     *
     * @param BusinessProfile|null $businessProfile
     *
     * @return BusinessProfilePhone
     */
    public function setBusinessProfile(BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }
}
