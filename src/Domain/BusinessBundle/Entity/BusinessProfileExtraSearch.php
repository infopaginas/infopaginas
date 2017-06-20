<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileExtraSearch as BusinessProfileExtraSearchValidator;

/**
 * BusinessProfileExtraSearch
 *
 * @ORM\Table(name="business_profile_extra_search")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileExtraSearchRepository")
 * @BusinessProfileExtraSearchValidator()
 */
class BusinessProfileExtraSearch
{
    const SERVICE_AREAS_AREA_CHOICE_VALUE = 'area';
    const SERVICE_AREAS_LOCALITY_CHOICE_VALUE = 'locality';

    const DEFAULT_MILES_FROM_MY_BUSINESS = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="extraSearches",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var Category[] - Business category
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Category",
     *     inversedBy="extraSearches",
     *     cascade={"persist"},
     *     orphanRemoval=false
     *     )
     * @ORM\JoinTable(name="business_profile_extra_search_categories")
     * @Assert\Valid
     * @Assert\Count(min = 1)
     */
    protected $categories;

    /**
     * @var string
     *
     * @ORM\Column(name="service_areas_type", type="string", options={"default": "area"})
     * @Assert\Choice(choices = {"area","locality"}, multiple = false, message = "business_profile.service_areas_type")
     */
    protected $serviceAreasType = 'area';

    /**
     * @var string
     *
     * @ORM\Column(name="miles_of_my_business", type="integer", nullable=true)
     * @Assert\NotBlank(groups={"service_area_chosen"})
     * @Assert\Type(type="integer", message="business_profile.integer_miles")
     * @Assert\Length(max=4, maxMessage="business_profile.max_length")
     * @Assert\GreaterThan(0)
     */
    protected $milesOfMyBusiness;

    /**
     * @var Area[] - Using this field a User may define Areas, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Area",
     *     inversedBy="extraSearches",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_extra_search_areas")
     */
    protected $areas;

    /**
     * @var Locality[] - Using this field a User may define Localities, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Locality",
     *     inversedBy="extraSearches",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_extra_search_localities")
     */
    protected $localities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->localities = new ArrayCollection();
        $this->areas      = new ArrayCollection();

        $this->milesOfMyBusiness = self::DEFAULT_MILES_FROM_MY_BUSINESS;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getId()) {
            $categories = $this->getCategories();
            $areas      = $this->getAreas();
            $localities = $this->getLocalities();

            $categoriesList = [];
            $areasList      = [];
            $localitiesList = [];

            foreach ($categories as $category) {
                $categoriesList[] = $category->getName();
            }

            foreach ($areas as $area) {
                $areasList[] = $area->getName();
            }

            foreach ($localities as $locality) {
                $localitiesList[] = $locality->getName();
            }

            $result = sprintf(
                '%s: Categories: %s; Areas: %s; Localities: %s; Type: %s; Miles %s',
                $this->getId(),
                implode(', ', $categoriesList),
                implode(', ', $areasList),
                implode(', ', $localitiesList),
                $this->getServiceAreasType(),
                $this->getMilesOfMyBusiness()
            );
        } else {
            $result = '';
        }

        return $result;
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
     * Set businessProfile
     *
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessProfileExtraSearch
     */
    public function setBusinessProfile(BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return BusinessProfile
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return string
     */
    public function getServiceAreasType()
    {
        return $this->serviceAreasType;
    }

    /**
     * @param string $serviceAreasType
     *
     * @return BusinessProfileExtraSearch
     */
    public function setServiceAreasType($serviceAreasType)
    {
        $this->serviceAreasType = $serviceAreasType;
        return $this;
    }

    /**
     * @return string
     */
    public function getMilesOfMyBusiness()
    {
        return $this->milesOfMyBusiness;
    }

    /**
     * @param string $milesOfMyBusiness
     *
     * @return BusinessProfileExtraSearch
     */
    public function setMilesOfMyBusiness($milesOfMyBusiness)
    {
        $this->milesOfMyBusiness = $milesOfMyBusiness;

        return $this;
    }

    /**
     * Add locality
     *
     * @param Locality $locality
     *
     * @return BusinessProfileExtraSearch
     */
    public function addLocality(Locality $locality)
    {
        $this->localities[] = $locality;

        return $this;
    }

    /**
     * Remove locality
     *
     * @param Locality $locality
     */
    public function removeLocality(Locality $locality)
    {
        $this->localities->removeElement($locality);
    }

    /**
     * @return ArrayCollection
     */
    public function getLocalities()
    {
        return $this->localities;
    }

    /**
     * Add area
     *
     * @param Area $area
     *
     * @return BusinessProfileExtraSearch
     */
    public function addArea(Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remove area
     *
     * @param Area $area
     */
    public function removeArea($area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * @return ArrayCollection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    public static function getServiceAreasTypes()
    {
        return [
            self::SERVICE_AREAS_AREA_CHOICE_VALUE       => 'Distance',
            self::SERVICE_AREAS_LOCALITY_CHOICE_VALUE   => 'Locality',
        ];
    }
}
