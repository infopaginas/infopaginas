<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\VO\Url;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
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
    use PersonalTranslatableTrait;
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
     * @var Url|null
     *
     * @ORM\Column(name="url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $urlItem;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->urlItem = new Url();
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
     * @return Url|null
     */
    public function getUrlItem()
    {
        return $this->urlItem;
    }

    /**
     * @param Url|null $urlItem
     * @return ClickbaitTitle
     */
    public function setUrlItem($urlItem)
    {
        $this->urlItem = $urlItem;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlItemLink()
    {
        $urlItem = $this->getUrlItem();

        if ($urlItem && $urlItem->getUrl()) {
            return $urlItem->getUrl();
        }

        return '';
    }
}
