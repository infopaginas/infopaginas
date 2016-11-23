<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CategoryRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("name")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\CategoryTranslation")
 * @Gedmo\Tree(type="materializedPath")
 */
class Category implements DefaultEntityInterface, CopyableEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    const TYPE_CATEGORY    = 'type_category';
    const TYPE_SUBCATEGORY = 'type_subcategory';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Gedmo\TreePathSource
     */
    protected $id;

    /**
     * @var string - Category name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="search_text_en", type="string", length=100, nullable=true)
     */
    protected $searchTextEn;

    /**
     * @var string
     *
     * @ORM\Column(name="search_text_es", type="string", length=100, nullable=true)
     */
    protected $searchTextEs;

    /**
     * @var BusinessProfile[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="categories"
     * )
     */
    protected $businessProfiles;

    /**
     * @ORM\OneToOne(targetEntity="Domain\MenuBundle\Entity\Menu", mappedBy="category", cascade={"persist"})
     */
    protected $menu;

    /**
     * @var Article[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ArticleBundle\Entity\Article",
     *     mappedBy="category",
     *     cascade={"persist"}
     *     )
     */
    protected $articles;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=100)
     */
    protected $slug;

    /**
     * @var CategoryTranslation[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\CategoryTranslation",
     *     mappedBy="object",
     *     cascade={"persist"}
     * )
     */
    protected $translations;

    /**
     * @var string
     *
     * @ORM\Column(name="search_fts_en", type="tsvector", options={
     *      "customSchemaOptions": {
     *          "searchFields" : {
     *              "searchTextEn"
     *          }
     *      }
     *  }, nullable=true)
     *
     */
    protected $searchFtsEn;

    /**
     * @var string
     *
     * @ORM\Column(name="search_fts_es", type="tsvector", options={
     *      "customSchemaOptions": {
     *          "searchFields" : {
     *              "searchTextEs"
     *          }
     *      }
     *  }, nullable=true)
     *
     */
    protected $searchFtsEs;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    protected $locale;

    /**
     * @Gedmo\TreePath
     * @ORM\Column(length=3000, nullable=true)
     */
    private $path;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    public function setLocale($locale)
    {
        $this->locale = $locale;
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
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfiles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?: '';
    }

    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set searchTextEn
     *
     * @param string $searchTextEn
     *
     * @return Category
     */
    public function setSearchTextEn($searchTextEn)
    {
        $this->searchTextEn = $searchTextEn;

        return $this;
    }

    /**
     * Get searchTextEn
     *
     * @return string
     */
    public function getSearchTextEn()
    {
        return $this->searchTextEn;
    }

    /**
     * Set searchTextEs
     *
     * @param string $searchTextEs
     *
     * @return Category
     */
    public function setSearchTextEs($searchTextEs)
    {
        $this->searchTextEs = $searchTextEs;

        return $this;
    }

    /**
     * Get searchTextEs
     *
     * @return string
     */
    public function getSearchTextEs()
    {
        return $this->searchTextEs;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Category
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles[] = $businessProfile;
        $businessProfile->addCategory($this);

        return $this;
    }

    /**
     * Remove businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles->removeElement($businessProfile);
        $businessProfile->removeCategory($this);
    }

    /**
     * Get businessProfiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessProfiles()
    {
        return $this->businessProfiles;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\CategoryTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\CategoryTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set menu
     *
     * @param \Domain\MenuBundle\Entity\Menu $menu
     *
     * @return Category
     */
    public function setMenu(\Domain\MenuBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \Domain\MenuBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param AbstractPersonalTranslation $translation
     *
     * @return $this
     */
    public function addTranslation(AbstractPersonalTranslation $translation)
    {
        $this->translations[] = $this;

        return $this;
    }


    /**
     * Add article
     *
     * @param \Domain\ArticleBundle\Entity\Article $article
     *
     * @return Category
     */
    public function addArticle(\Domain\ArticleBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Domain\ArticleBundle\Entity\Article $article
     */
    public function removeArticle(\Domain\ArticleBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Category
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
     * Set searchFtsEn
     *
     * @param tsvector $searchFtsEn
     *
     * @return Category
     */
    public function setSearchFtsEn($searchFtsEn)
    {
        $this->searchFtsEn = $searchFtsEn;

        return $this;
    }

    /**
     * Get searchFtsEn
     *
     * @return tsvector
     */
    public function getSearchFtsEn()
    {
        return $this->searchFtsEn;
    }

    /**
     * Set searchFtsEs
     *
     * @param tsvector $searchFtsEs
     *
     * @return Category
     */
    public function setSearchFtsEs($searchFtsEs)
    {
        $this->searchFtsEs = $searchFtsEs;

        return $this;
    }

    /**
     * Get searchFtsEs
     *
     * @return tsvector
     */
    public function getSearchFtsEs()
    {
        return $this->searchFtsEs;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setLvl($level)
    {
        $this->lvl = $level;
    }

    public function getLvl()
    {
        return $this->lvl;
    }

    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child category
     *
     * @param Category
     *
     * @return Category
     */
    public function addChild(Category $category)
    {
        $this->children[] = $category;
        $category->setParent($this);

        return $this;
    }

    /**
     * Remove child category
     *
     * @param Category $category
     */
    public function removeChild(Category $category)
    {
        $this->children->removeElement($category);
        $category->setParent(null);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getCategoryType()
    {
        return (bool)$this->getParent() ? self::TYPE_SUBCATEGORY : self::TYPE_CATEGORY;
    }
}
