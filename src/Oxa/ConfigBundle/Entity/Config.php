<?php

namespace Oxa\ConfigBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Config
 *
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="Oxa\ConfigBundle\Repository\ConfigRepository")
 * @UniqueEntity("key")
 * @Gedmo\TranslationEntity(class="Oxa\ConfigBundle\Entity\Translation\ConfigTranslation")
 */
class Config implements DefaultEntityInterface, ConfigInterface, TranslatableInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use ChangeStateTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="string", length=255)
     */
    protected $key;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text")
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=255)
     */
    protected $format;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Oxa\ConfigBundle\Entity\Translation\ConfigTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return Config
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Config
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Config
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return Config
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Config
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Remove translation
     *
     * @param \Oxa\ConfigBundle\Entity\Translation\ConfigTranslation $translation
     */
    public function removeTranslation(\Oxa\ConfigBundle\Entity\Translation\ConfigTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
