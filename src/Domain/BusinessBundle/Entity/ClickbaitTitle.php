<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="clickbait_title")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\ClickbaitTitleRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\ClickbaitTranslation")
 * @UniqueEntity("locality")
 */
class ClickbaitTitle implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
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
     * @var $locality
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Locality")
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotBlank()
     */
    protected $locality;

    /**
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(name="url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @Assert\Url()
     * @Assert\NotBlank()
     */
    protected $url;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLocality() ? $this->getLocality()->getName() : (string)$this->getId();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Locality|null
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param Locality|null $locality
     *
     * @return ClickbaitTitle
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ClickbaitTitle
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return ClickbaitTitle
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
