<?php

namespace Domain\BannerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="Domain\BannerBundle\Repository\BannerRepository")
 * @Gedmo\TranslationEntity(class="Domain\BannerBundle\Entity\Translation\BannerTranslation")
 */
class Banner implements DefaultEntityInterface, TranslatableInterface, CopyableEntityInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Banner title
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string - Banner description
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", length=100)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @var string - Using this checkbox a User may define whether the banner is used for advertising Businesses
     * or for other purposes (Google AdSense, custom ad, etc)
     *
     * @ORM\Column(name="allowed_for_businesses", type="boolean", options={"default" : 1})
     */
    protected $allowedForBusinesses = true;

    /**
     * @var Template - Banner Template, If a User selects template,
     * all entered data of “Type”, “Size” and “Body” fields are overwritten by template data.
     *
     * @ORM\ManyToOne(targetEntity="Domain\BannerBundle\Entity\Template",
     *     inversedBy="banners",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $template;

    /**
     * @var Type - Banner type
     * @ORM\ManyToOne(targetEntity="Domain\BannerBundle\Entity\Type",
     *     inversedBy="banners",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    protected $type;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    protected $position;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Translation\BannerTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var string - Using this checkbox a Admin may define whether to show a banner block.
     *
     * @ORM\Column(name="is_published", type="boolean", options={"default" : 0})
     */
    protected $isPublished;

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
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isPublished  = false;
    }

    /**
     * @return string
     */
    public function getMarkCopyPropertyName()
    {
        return 'title';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->getType()->getSize();
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Banner
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
     * Set description
     *
     * @param string $description
     *
     * @return Banner
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
     * Set allowedForBusinesses
     *
     * @param boolean $allowedForBusinesses
     *
     * @return Banner
     */
    public function setAllowedForBusinesses($allowedForBusinesses)
    {
        $this->allowedForBusinesses = $allowedForBusinesses;

        return $this;
    }

    /**
     * Get allowedForBusinesses
     *
     * @return boolean
     */
    public function getAllowedForBusinesses()
    {
        return $this->allowedForBusinesses;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Banner
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

    /**
     * Set template
     *
     * @param \Domain\BannerBundle\Entity\Template $template
     *
     * @return Banner
     */
    public function setTemplate(\Domain\BannerBundle\Entity\Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Domain\BannerBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set type
     *
     * @param \Domain\BannerBundle\Entity\Type $type
     *
     * @return Banner
     */
    public function setType(\Domain\BannerBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Domain\BannerBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BannerBundle\Entity\Translation\BannerTranslation $translation
     */
    public function removeTranslation(\Domain\BannerBundle\Entity\Translation\BannerTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Banner
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }
}
