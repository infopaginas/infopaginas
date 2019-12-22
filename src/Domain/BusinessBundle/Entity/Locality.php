<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\LocalityTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\BusinessBundle\Entity\Area;
use Oxa\GeolocationBundle\Utils\Traits\LocationTrait;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;

/**
 * Locality
 *
 * @ORM\Table(name="locality")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LocalityRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\LocalityTranslation")
 */
class Locality implements
    GeolocationInterface,
    DefaultEntityInterface,
    OxaPersonalTranslatableInterface,
    ChangeStateInterface
{
    use DefaultEntityTrait;
    use LocationTrait;
    use PersonalTranslatable;
    use ChangeStateTrait;

    const ALL_LOCALITY = 'PR';
    const ALL_LOCALITY_NAME = 'Puerto Rico';
    const DEFAULT_CATALOG_LOCALITY_SLUG = 'san-juan';
    const ALLOW_DELETE_ASSOCIATED_FIELD_BUSINESS_PROFILES = 'businessProfiles';
    const ALLOW_DELETE_ASSOCIATED_FIELD_CATALOG_ITEMS     = 'catalogItems';

    const ELASTIC_DOCUMENT_TYPE = 'Locality';
    const ELASTIC_INDEX = 'locality';

    const FLAG_IS_UPDATED       = 'isUpdated';
    const LOCALITY_FIELD_NAME   = 'name';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Locality name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="localities",
     *     cascade={"persist"}
     * )
     */
    private $businessProfile;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileExtraSearch",
     *     mappedBy="localities"
     * )
     */
    private $extraSearches;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\Area",
     *      inversedBy="locality"
     * )
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $area;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\Neighborhood",
     *      mappedBy="locality",
     *      cascade={"persist"}
     * )
     */
    protected $neighborhoods;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\LocalityTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=100)
     */
    protected $slug;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *      mappedBy="catalogLocality",
     *      cascade={"persist"}
     * )
     */
    protected $businessProfiles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\CatalogItem",
     *      mappedBy="locality",
     * )
     */
    protected $catalogItems;

    /**
     * Related to FLAG_IS_UPDATED const
     * @var bool
     *
     * @ORM\Column(name="is_updated", type="boolean", options={"default" : 1})
     */
    protected $isUpdated;

    /**
     * @var LocalityPseudo[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\LocalityPseudo",
     *     mappedBy="locality",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $pseudos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfile  = new ArrayCollection();
        $this->businessProfiles = new ArrayCollection();
        $this->translations     = new ArrayCollection();
        $this->neighborhoods    = new ArrayCollection();
        $this->catalogItems     = new ArrayCollection();
        $this->pseudos          = new ArrayCollection();
        $this->extraSearches    = new ArrayCollection();

        $this->isUpdated = true;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Locality
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @param mixed $businessProfile
     * @return Locality
     */
    public function setBusinessProfile($businessProfile)
    {
        $this->businessProfile = $businessProfile;
        return $this;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Locality
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfile[] = $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return $this
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfile->removeElement($businessProfile);

        return $this;
    }

    /**
     * Add $extraSearch
     *
     * @param BusinessProfileExtraSearch $extraSearch
     *
     * @return Locality
     */
    public function addExtraSearch($extraSearch)
    {
        $this->extraSearches[] = $extraSearch;
        $extraSearch->addLocality($this);
    }

    /**
     * @param BusinessProfileExtraSearch $extraSearch
     *
     * @return Locality
     */
    public function removeExtraSearch(BusinessProfileExtraSearch $extraSearch)
    {
        $this->extraSearches->removeElement($extraSearch);
        $extraSearch->removeLocality($this);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getExtraSearches()
    {
        return $this->extraSearches;
    }

    /**
     * @return mixed
     */
    public function getBusinessProfiles()
    {
        return $this->businessProfiles;
    }

    /**
     * @param mixed $businessProfiles
     * @return Locality
     */
    public function setBusinessProfiles($businessProfiles)
    {
        $this->businessProfiles = $businessProfiles;
        return $this;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfiles
     *
     * @return Locality
     */
    public function addBusinessProfiles(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfiles)
    {
        $this->businessProfiles[] = $businessProfiles;
    }

    /**
     * @param BusinessProfile $businessProfiles
     * @return $this
     */
    public function removeBusinessProfiles(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfiles)
    {
        $this->businessProfiles->removeElement($businessProfiles);

        return $this;
    }

    /**
     *  Get owning area for this locality
     *
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     *  Set owning area for this locality
     *
     * @param Area $area
     * @return $this
     */
    public function setArea(Area $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get Neighborhoods
     *
     * @return Neighborhood[]
     */
    public function getNeighborhoods()
    {
        return $this->neighborhoods;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
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
     * @return Locality
     */
    public function addCatalogItem(CatalogItem $catalogItem)
    {
        $this->catalogItems[] = $catalogItem;

        return $this;
    }

    /**
     * @param CatalogItem $catalogItem
     *
     * @return Locality
     */
    public function removeCatalogItems(CatalogItem $catalogItem)
    {
        $this->catalogItems->removeElement($catalogItem);

        return $this;
    }

    /**
     * @param boolean $isUpdated
     *
     * @return Locality
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
     * @return ArrayCollection
     */
    public function getPseudos()
    {
        return $this->pseudos;
    }

    /**
     * Add $pseudo
     *
     * @param LocalityPseudo $pseudo
     *
     * @return Locality
     */
    public function addPseudo($pseudo)
    {
        $this->pseudos[] = $pseudo;

        $pseudo->setLocality($this);

        return $this;
    }

    /**
     * Remove pseudo
     *
     * @param LocalityPseudo $pseudo
     */
    public function removePseudo($pseudo)
    {
        $this->pseudos->removeElement($pseudo);
    }

    public function getTranslationClass(): string
    {
        return LocalityTranslation::class;
    }
}
