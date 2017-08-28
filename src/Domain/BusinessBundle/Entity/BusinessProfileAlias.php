<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessProfileKeyword
 *
 * @ORM\Table(name="business_profile_alias")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileAliasRepository")
 */
class BusinessProfileAlias
{
    const REGEX_SLUG_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

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
     * @ORM\Column(name="slug", type="string", length=255)
     * @Assert\NotBlank(message="business_profile.alias.not_blank")
     * @Assert\Length(
     *      max="255",
     *      maxMessage="business_profile.alias.max_length",
     *  )
     */
    private $slug;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="aliases",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getSlug();
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
     * @param string $slug
     *
     * @return BusinessProfileAlias
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
