<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="business_profile_media_urls",
 *     uniqueConstraints={
 *        @UniqueConstraint(name="type_unique",
 *            columns={"business_profile_id", "type"})
 *    })
 * @ORM\Entity()
 */
class BusinessProfileMediaUrl
{
    const TYPE_INSTAGRAM = 'instagram';
    const TYPE_FACEBOOK = 'facebook';
    const TYPE_TWITTER = 'twitter';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - url type
     *
     * @ORM\Column(name="type", type="string", length=32)
     * @Assert\Length(max=32)
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @var string - URL
     *
     * @ORM\Column(name="url", type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    protected $url;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="mediaUrls"
     * )
     */
    protected $businessProfile;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl() ?: '';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return BusinessProfileMediaUrl
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return BusinessProfileMediaUrl
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessProfileMediaUrl
     */
    public function setBusinessProfile(BusinessProfile $businessProfile)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BusinessProfileMediaUrl
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    public static function getAvailableTypes()
    {
        return [
            self::TYPE_INSTAGRAM => 'business_profile_media_url.type.instagram',
            self::TYPE_FACEBOOK  => 'business_profile_media_url.type.facebook',
            self::TYPE_TWITTER   => 'business_profile_media_url.type.twitter',
        ];
    }
}
