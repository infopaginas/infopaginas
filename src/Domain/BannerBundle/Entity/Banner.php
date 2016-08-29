<?php

namespace Domain\BannerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="Domain\BannerBundle\Repository\BannerRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
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
     * @var Media - Media Logo
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="banners",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=false)
     */
    protected $image;

    /**
     * @var string - Banner description
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", length=100)
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
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
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
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Campaign",
     *     mappedBy="banners",
     *     cascade={"persist"}
     *     )
     */
    protected $campaigns;

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
    }

    public function getMarkCopyPropertyName()
    {
        return 'title';
    }

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
     * Set image
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\Media $image
     *
     * @return Banner
     */
    public function setImage(\Oxa\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Oxa\Sonata\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add campaign
     *
     * @param \Domain\BannerBundle\Entity\Campaign $campaign
     *
     * @return Banner
     */
    public function addCampaign(\Domain\BannerBundle\Entity\Campaign $campaign)
    {
        $this->campaigns[] = $campaign;

        return $this;
    }

    /**
     * Remove campaign
     *
     * @param \Domain\BannerBundle\Entity\Campaign $campaign
     */
    public function removeCampaign(\Domain\BannerBundle\Entity\Campaign $campaign)
    {
        $this->campaigns->removeElement($campaign);
    }

    /**
     * Get campaigns
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }
}
