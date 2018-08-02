<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessProfileExtraSearch
 *
 * @ORM\Table(name="business_profile_suggest_edit")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileSuggestEditRepository")
 */
class BusinessProfileSuggestEdit implements DefaultEntityInterface
{
    use DefaultEntityTrait;

    const STATUS_NEW      = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    const KEY_LABELS = [
        'name'          => 'Name',
        'website'       => 'Website',
        'phones'        => 'Phones',
        'workingHours'  => 'Working Hours',
        'streetAddress' => 'Street address',
        'socialLinks'   => 'Social network links',
        'status'        => 'Business status',
        'map'           => 'Map pin incorrect',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="suggestEdits",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=32, nullable=false)
     * @Assert\Length(max="32")
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     * @Assert\Length(max="255")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=16, nullable=false)
     */
    private $status = self::STATUS_NEW;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return $this
     */
    public function setBusinessProfile(BusinessProfile $businessProfile)
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

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getKeyLabel()
    {
        if (!array_key_exists($this->getKey(), self::KEY_LABELS)) {
            throw new \InvalidArgumentException('Undefined label key');
        }

        return self::KEY_LABELS[$this->getKey()];
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
