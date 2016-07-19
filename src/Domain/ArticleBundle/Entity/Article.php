<?php

namespace Domain\ArticleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Category;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Domain\ArticleBundle\Repository\ArticleRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\ArticleBundle\Entity\Translation\ArticleTranslation")
 */
class Article implements DefaultEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string - Article title
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=100)
     */
    protected $title;

    /**
     * @var Media - Media
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $image;

    /**
     * @var string - Article description
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", length=100, nullable=true)
     */
    protected $description;

    /**
     * @var string - Body
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="body", type="text")
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
     * @Gedmo\Slug(fields={"title"})
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

    public function __toString()
    {
        switch (true) {
            case $this->getTitle():
                $result = $this->getTitle();
                break;
            case $this->getId():
                $result = sprintf('id(%s): not translated', $this->getId());
                break;
            default:
                $result = 'New article';
        }
        return $result;
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
     * Set description
     *
     * @param string $description
     *
     * @return Article
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
}
