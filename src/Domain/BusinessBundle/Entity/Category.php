<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\ReportBundle\Entity\CategoryReport;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CategoryRepository")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\CategoryTranslation")
 * @Gedmo\Tree(type="materializedPath")
 */
class Category implements DefaultEntityInterface, CopyableEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    const TYPE_CATEGORY_PATTERN = 'TYPE_CATEGORY_';

    const TYPE_CATEGORY_1 = 'type_category_1';
    const TYPE_CATEGORY_2 = 'type_category_2';
    const TYPE_CATEGORY_3 = 'type_category_3';

    const CATEGORY_DEFAULT_LEVEL    = 1;
    const SUBCATEGORY_DEFAULT_LEVEL = 2;
    const CATEGORY_MAX_LEVEL        = 3;

    const CATEGORY_LEVEL_1 = 1;
    const CATEGORY_LEVEL_2 = 2;
    const CATEGORY_LEVEL_3 = 3;

    const CATEGORY_FIELD_NAME = 'name';

    const CATEGORY_UNDEFINED_CODE = '54016';
    const CATEGORY_UNDEFINED_SLUG = 'undefined';

    const ELASTIC_DOCUMENT_TYPE = 'Category';
    const FLAG_IS_UPDATED = 'isUpdated';

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
     * Related to CATEGORY_FIELD_NAME
     * @var string - Category name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="search_text_en", type="string", length=255, nullable=true)
     */
    protected $searchTextEn;

    /**
     * @var string
     *
     * @ORM\Column(name="search_text_es", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string - Used to create human like url en
     *
     * @ORM\Column(name="slug_en", type="string", length=255, nullable=true)
     */
    protected $slugEn;

    /**
     * @var string - Used to create human like url en
     *
     * @ORM\Column(name="slug_es", type="string", length=255, nullable=true)
     */
    protected $slugEs;

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
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $children;


    /**
     * @var string - Category code
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * Related to FLAG_IS_UPDATED const
     * @var bool
     *
     * @ORM\Column(name="is_updated", type="boolean", options={"default" : 1})
     */
    protected $isUpdated;

    /** @var CategoryReport[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ReportBundle\Entity\CategoryReport",
     *     mappedBy="category",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $reports;

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
        $this->reports = new \Doctrine\Common\Collections\ArrayCollection();

        $this->isUpdated = true;
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
        return constant('self::' . self::TYPE_CATEGORY_PATTERN . $this->getLvl());
    }

    /**
     * @param string $slugEn
     *
     * @return Category
     */
    public function setSlugEn($slugEn)
    {
        $this->slugEn = $slugEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlugEn()
    {
        return $this->slugEn;
    }

    /**
     * @param string $slugEs
     *
     * @return Category
     */
    public function setSlugEs($slugEs)
    {
        $this->slugEs = $slugEs;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlugEs()
    {
        return $this->slugEs;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Category
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param boolean $isUpdated
     *
     * @return BusinessProfile
     */
    public function setIsUpdated($isUpdated)
    {
        $this->isUpdated = $isUpdated;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsUpdated()
    {
        return $this->isUpdated;
    }

    /**
     * Add category report
     *
     * @param CategoryReport $report
     * @return BusinessProfile
     */
    public function addReport(CategoryReport $report)
    {
        $this->reports[] = $report;
        return $this;
    }

    /** Remove category report
     *
     * @param CategoryReport $report
     */
    public function removeReport(CategoryReport $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get category reports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReports()
    {
        return $this->reports;
    }

    public static function getTranslatableFields()
    {
        return [
            self::CATEGORY_FIELD_NAME
        ];
    }
}
