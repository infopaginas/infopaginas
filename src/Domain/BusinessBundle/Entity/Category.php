<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CategoryRepository")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\CategoryTranslation")
 */
class Category implements
    DefaultEntityInterface,
    CopyableEntityInterface,
    OxaPersonalTranslatableInterface,
    ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use ChangeStateTrait;

    const CATEGORY_FIELD_NAME = 'name';
    const CATEGORY_LOCALE_PROPERTY = 'searchText';

    const CATEGORY_UNDEFINED_CODE = '54016';
    const CATEGORY_UNDEFINED_SLUG = 'unclassified';

    const CATEGORY_ARTICLE_CODE = '99999';
    const CATEGORY_ARTICLE_SLUG = 'infopaginas-media';

    const ELASTIC_DOCUMENT_TYPE = 'Category';
    const FLAG_IS_UPDATED = 'isUpdated';

    const ALLOW_DELETE_ASSOCIATED_FIELD_CATALOG_ITEMS = 'catalogItems';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @var BusinessProfileExtraSearch[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileExtraSearch",
     *     mappedBy="categories"
     * )
     */
    protected $extraSearches;

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

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\CatalogItem",
     *      mappedBy="category",
     * )
     */
    protected $catalogItems;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_suggestion", type="boolean", options={"default" : 0})
     */
    protected $showSuggestion;

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
        $this->businessProfiles = new ArrayCollection();
        $this->translations     = new ArrayCollection();
        $this->articles         = new ArrayCollection();
        $this->reports          = new ArrayCollection();
        $this->catalogItems     = new ArrayCollection();
        $this->extraSearches    = new ArrayCollection();

        $this->isUpdated = true;
        $this->showSuggestion = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSearchTextEn() . ' / ' . $this->getSearchTextEs();
    }

    /**
     * @return string
     */
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
     * Add extraSearch
     *
     * @param BusinessProfileExtraSearch $extraSearch
     *
     * @return Category
     */
    public function addExtraSearch($extraSearch)
    {
        $this->extraSearches[] = $extraSearch;
        $extraSearch->addCategory($this);

        return $this;
    }

    /**
     * Remove extraSearch
     *
     * @param BusinessProfileExtraSearch $extraSearch
     */
    public function removeExtraSearch($extraSearch)
    {
        $this->extraSearches->removeElement($extraSearch);
        $extraSearch->removeCategory($this);
    }

    /**
     * Get $extraSearches
     *
     * @return ArrayCollection
     */
    public function getExtraSearches()
    {
        return $this->extraSearches;
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

    /**
     * @return ArrayCollection
     */
    public function getCatalogItems()
    {
        return $this->catalogItems;
    }

    /**
     * Add catalogItem
     *
     * @param CatalogItem $catalogItem
     *
     * @return Category
     */
    public function addCatalogItem(CatalogItem $catalogItem)
    {
        $this->catalogItems[] = $catalogItem;

        return $this;
    }

    /**
     * @param CatalogItem $catalogItem
     *
     * @return Category
     */
    public function removeCatalogItems(CatalogItem $catalogItem)
    {
        $this->catalogItems->removeElement($catalogItem);

        return $this;
    }

    /**
     * @param bool $showSuggestion
     *
     * @return Category
     */
    public function setShowSuggestion($showSuggestion)
    {
        $this->showSuggestion = $showSuggestion;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowSuggestion()
    {
        return $this->showSuggestion;
    }

    /**
     * @return array
     */
    public static function getDefaultCategories()
    {
        return [
            self::CATEGORY_ARTICLE_CODE,
            self::CATEGORY_UNDEFINED_CODE,
        ];
    }

    /**
     * @return array
     */
    public static function getSystemCategorySlugs()
    {
        return [
            self::CATEGORY_ARTICLE_SLUG,
            self::CATEGORY_UNDEFINED_SLUG,
        ];
    }

    public function getTranslationClass(): string
    {
        return CategoryTranslation::class;
    }
}
