<?php

namespace Domain\ArticleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\ArticleBundle\Entity\Media\ArticleGallery;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Util\Traits\DatetimePeriodStatusTrait;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Domain\SiteBundle\Utils\Traits\SeoTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Domain\ArticleBundle\Repository\ArticleRepository")
 * @Gedmo\TranslationEntity(class="Domain\ArticleBundle\Entity\Translation\ArticleTranslation")
 */
class Article implements DefaultEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use SeoTrait;

    const ARTICLE_FIELD_TITLE = 'title';
    const ARTICLE_FIELD_BODY  = 'body';

    const ARTICLE_FIELD_SEO_TITLE        = 'seoTitle';
    const ARTICLE_FIELD_SEO_DESCRIPTION  = 'seoDescription';

    const ARTICLE_TITLE_MAX_LENGTH  = 100;
    const ARTICLE_BODY_MAX_LENGTH   = 10000;
    const ARTICLE_AUTHOR_MAX_LENGTH = 255;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Article title
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    protected $title;

    /**
     * @var Media - Media
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="articles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $image;

    /**
     * @var string - Body
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="body", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(max=10000)
     */
    protected $body;

    /**
     * @var string - Using this checkbox a User may define whether to show an article.
     *
     * @ORM\Column(name="is_published", type="boolean", options={"default" : 0})
     */
    protected $isPublished = false;

    /**
     * @var string - Using this checkbox a User may define whether to show an article on homepage.
     *
     * @ORM\Column(name="is_on_homepage", type="boolean", options={"default" : 0})
     */
    protected $isOnHomepage = false;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(name="slug", type="string")
     */
    protected $slug;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ArticleBundle\Entity\Translation\ArticleTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Category",
     *     inversedBy="articles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected $category;

    /**
     * @var \DateTime
     * @ORM\Column(name="activation_date", type="datetime")
     */
    protected $activationDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true)
     */
    protected $expirationDate;

    /**
     * @var int
     *
     * @ORM\Column(name="external_id", type="integer", nullable=true)
     */
    protected $externalId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_external", type="boolean", options={"default" : 0})
     */
    protected $isExternal = false;

    /**
     * @var string
     *
     * @ORM\Column(name="author_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    protected $authorName;

    /**
     * @var ArticleGallery[] - Media Images
     * @ORM\OneToMany(targetEntity="Domain\ArticleBundle\Entity\Media\ArticleGallery",
     *     mappedBy="article",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     *     )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $images;

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
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        $datetime = new \DateTime('now');
        $diff = $datetime->diff($this->getExpirationDate());

        return boolval($diff->invert);
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Article
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
     * Set body
     *
     * @param string $body
     *
     * @return Article
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Article
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

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set image
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\Media $image
     *
     * @return Article
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
     * Remove translation
     *
     * @param \Domain\ArticleBundle\Entity\Translation\ArticleTranslation $translation
     */
    public function removeTranslation(\Domain\ArticleBundle\Entity\Translation\ArticleTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     *
     * @return Article
     */
    public function setCategory(\Domain\BusinessBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Domain\BusinessBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set isOnHomepage
     *
     * @param boolean $isOnHomepage
     *
     * @return Article
     */
    public function setIsOnHomepage($isOnHomepage)
    {
        $this->isOnHomepage = $isOnHomepage;

        return $this;
    }

    /**
     * Get isOnHomepage
     *
     * @return boolean
     */
    public function getIsOnHomepage()
    {
        return $this->isOnHomepage;
    }

    /**
     * Set activationDate
     *
     * @param \DateTime $activationDate
     *
     * @return Article
     */
    public function setActivationDate($activationDate)
    {
        $this->activationDate = $activationDate;

        return $this;
    }

    /**
     * Get activationDate
     *
     * @return \DateTime
     */
    public function getActivationDate()
    {
        return $this->activationDate;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return Article
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     * @return Article
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsExternal()
    {
        return $this->isExternal;
    }

    /**
     * @param bool $isExternal
     * @return Article
     */
    public function setIsExternal($isExternal)
    {
        $this->isExternal = $isExternal;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     * @return Article
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArticleGallery $image
     */
    public function addImage(ArticleGallery $image)
    {
        $this->images->add($image);
        $image->setArticle($this);
        return $this;
    }

    /**
     * @param ArticleGallery $image
     */
    public function removeImage(ArticleGallery $image)
    {
        $this->images->removeElement($image);
        return $this;
    }
}
