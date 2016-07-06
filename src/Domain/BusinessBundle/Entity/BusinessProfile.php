<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BannerBundle\Entity\Campaign;
use Domain\BusinessBundle\Entity\Address\Country;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task\Task;
use Domain\BusinessBundle\Model\StatusInterface;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="business_profile")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation")
 */
class BusinessProfile implements DefaultEntityInterface, CopyableEntityInterface, TranslatableInterface
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
    protected $id;

    /**
     * @var string - Business name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var User - Business owner
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var Subscription[] - Business subscriptions
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Subscription",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @ORM\OrderBy({"status" = "ASC"})
     */
    protected $subscriptions;

    /**
     * @var Discount[] - Business Discounts
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Discount",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @ORM\OrderBy({"status" = "ASC"})
     */
    protected $discounts;

    /**
     * @var Campaign[] - Business Campaigns
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Campaign",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @ORM\OrderBy({"status" = "ASC"})
     */
    protected $campaigns;

    /**
     * @var Category[] - Business category
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Category",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_categories")
     */
    protected $categories;

    /**
     * @var string - Website
     *
     * @ORM\Column(name="website", type="string", length=30)
     */
    protected $website;

    /**
     * @var string - Email address
     *
     * @ORM\Column(name="email", type="string", length=30, nullable=true)
     */
    protected $email;

    /**
     * @var string - Contact phone number
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    protected $phone;

    /**
     * @var \DateTime - Date of registration in Infopaginas
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="registration_date", type="datetime")
     */
    protected $registrationDate;

    /**
     * @var Area[] - Using this field a User may define Areas, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Area",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_areas")
     */
    protected $areas;

    /**
     * @var string - Slogan of a Business
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="slogan", type="string", length=255, nullable=true)
     */
    protected $slogan;

    /**
     * @var Tag[] - Tags related to Profile
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Tag",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_tags")
     */
    protected $tags;

    /**
     * @var string - Description of Business
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @var string - Products of Business
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="product", type="text", length=1000, nullable=true)
     */
    protected $product;

    /**
     * @var string - Operational Hours
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="working_hours", type="string", length=255, nullable=true)
     */
    protected $workingHours;

    /**
     * @var Brand[] - Brands, Business works with
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Brand",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_brands")
     */
    protected $brands;

    /**
     * @var PaymentMethod[] - Contains list of Payment Methods
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_payment_methods")
     */
    protected $paymentMethods;

    /**
     * @var string - Field is checked, if Description field of profile is set.
     *
     * @ORM\Column(name="is_set_description", type="boolean", options={"default" : 0})
     */
    protected $isSetDescription = false;

    /**
     * @var string - Field is checked, if business is marked on map.
     *
     * @ORM\Column(name="is_set_map", type="boolean", options={"default" : 0})
     */
    protected $isSetMap = false;

    /**
     * @var string - Field is checked, if Ad is defined.
     *
     * @ORM\Column(name="is_set_ad", type="boolean", options={"default" : 0})
     */
    protected $isSetAd = false;

    /**
     * @var string - Field is checked, if Logo field of profile is set.
     *
     * @ORM\Column(name="is_set_logo", type="boolean", options={"default" : 0})
     */
    protected $isSetLogo = false;

    /**
     * @var string - Field is checked, if Slogan field of profile is set.
     *
     * @ORM\Column(name="is_set_slogan", type="boolean", options={"default" : 0})
     */
    protected $isSetSlogan = false;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=100)
     */
    protected $slug;

    /**
     * @var Task[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Task\Task", mappedBy="businessProfile")
     */
    protected $tasks;

    /**
     * @var BusinessReview[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Review\BusinessReview",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $businessReviews;

    /**
     * @var BusinessGallery[] - Media Images
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Media\BusinessGallery",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     *     )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $images;

    /**
     * @var Media - Media Logo
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    protected $logo;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    protected $position;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var string
     *
     * @ORM\Column(name="street_address", type="string", length=50, nullable=true)
     */
    protected $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="string", length=50, nullable=true)
     */
    protected $streetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="extended_address", type="string", length=50, nullable=true)
     */
    protected $extendedAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="full_address", type="string", nullable=true)
     */
    protected $fullAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=30, nullable=true)
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="custom_address", type="string", nullable=true)
     */
    protected $customAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="use_map_address", type="boolean", options={"default" : 1})
     */
    protected $useMapAddress = true;

    /**
     * @var string - If checkbox is checked, both address of Business and mark on map are not shown to Consumer.
     *
     * @ORM\Column(name="hide_address", type="boolean", options={"default" : 0})
     */
    protected $hideAddress = false;

    /**
     * @var Country - Country, Business is located in
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Address\Country",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="search_fts", type="tsvector", options={
     *      "customSchemaOptions": {
     *          "searchFields" : {
     *              "name",
     *              "description"
     *          }
     *      }
     *  }, nullable=true)
     *
     */
    protected $searchFts;

    /**
     * @var string
     *
     * @ORM\Column(name="search_name_fts", type="tsvector", options={
     *      "customSchemaOptions": {
     *          "searchFields" : {
     *              "name"
     *          }
     *      }
     *  }, nullable=true)
     *
     */
    protected $searchNameFts;

    /**
     * @var string
     *
     * @ORM\Column(name="search_city_fts", type="tsvector", options={
     *      "customSchemaOptions": {
     *          "searchFields" : {
     *              "city"
     *          }
     *      }
     *  }, nullable=true)
     *
     */
    protected $searchCityFts;

    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    public function __toString()
    {
        return ($this->getName()) ?: 'New business';
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
        $this->discounts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subscriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->brands = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->businessReviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BusinessProfile
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
     * Set website
     *
     * @param string $website
     *
     * @return BusinessProfile
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return BusinessProfile
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return BusinessProfile
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return BusinessProfile
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set slogan
     *
     * @param string $slogan
     *
     * @return BusinessProfile
     */
    public function setSlogan($slogan)
    {
        $this->slogan = $slogan;

        return $this;
    }

    /**
     * Get slogan
     *
     * @return string
     */
    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return BusinessProfile
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
     * Set product
     *
     * @param string $product
     *
     * @return BusinessProfile
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set workingHours
     *
     * @param string $workingHours
     *
     * @return BusinessProfile
     */
    public function setWorkingHours($workingHours)
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    /**
     * Get workingHours
     *
     * @return string
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set isSetDescription
     *
     * @param boolean $isSetDescription
     *
     * @return BusinessProfile
     */
    public function setIsSetDescription($isSetDescription)
    {
        $this->isSetDescription = $isSetDescription;

        return $this;
    }

    /**
     * Get isSetDescription
     *
     * @return boolean
     */
    public function getIsSetDescription()
    {
        return $this->isSetDescription;
    }

    /**
     * Set isSetMap
     *
     * @param boolean $isSetMap
     *
     * @return BusinessProfile
     */
    public function setIsSetMap($isSetMap)
    {
        $this->isSetMap = $isSetMap;

        return $this;
    }

    /**
     * Get isSetMap
     *
     * @return boolean
     */
    public function getIsSetMap()
    {
        return $this->isSetMap;
    }

    /**
     * Set isSetAd
     *
     * @param boolean $isSetAd
     *
     * @return BusinessProfile
     */
    public function setIsSetAd($isSetAd)
    {
        $this->isSetAd = $isSetAd;

        return $this;
    }

    /**
     * Get isSetAd
     *
     * @return boolean
     */
    public function getIsSetAd()
    {
        return $this->isSetAd;
    }

    /**
     * Set isSetLogo
     *
     * @param boolean $isSetLogo
     *
     * @return BusinessProfile
     */
    public function setIsSetLogo($isSetLogo)
    {
        $this->isSetLogo = $isSetLogo;

        return $this;
    }

    /**
     * Get isSetLogo
     *
     * @return boolean
     */
    public function getIsSetLogo()
    {
        return $this->isSetLogo;
    }

    /**
     * Set isSetSlogan
     *
     * @param boolean $isSetSlogan
     *
     * @return BusinessProfile
     */
    public function setIsSetSlogan($isSetSlogan)
    {
        $this->isSetSlogan = $isSetSlogan;

        return $this;
    }

    /**
     * Get isSetSlogan
     *
     * @return boolean
     */
    public function getIsSetSlogan()
    {
        return $this->isSetSlogan;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return BusinessProfile
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
     * Set user
     *
     * @param \Oxa\Sonata\UserBundle\Entity\User $user
     *
     * @return BusinessProfile
     */
    public function setUser(\Oxa\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Oxa\Sonata\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     *
     * @return BusinessProfile
     */
    public function addCategory(\Domain\BusinessBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     */
    public function removeCategory(\Domain\BusinessBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add area
     *
     * @param \Domain\BusinessBundle\Entity\Area $area
     *
     * @return BusinessProfile
     */
    public function addArea(\Domain\BusinessBundle\Entity\Area $area)
    {
        $this->areas[] = $area;
        return $this;
    }

    /**
     * Remove area
     *
     * @param \Domain\BusinessBundle\Entity\Area $area
     */
    public function removeArea(\Domain\BusinessBundle\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Add tag
     *
     * @param \Domain\BusinessBundle\Entity\Tag $tag
     *
     * @return BusinessProfile
     */
    public function addTag(\Domain\BusinessBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Domain\BusinessBundle\Entity\Tag $tag
     */
    public function removeTag(\Domain\BusinessBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add brand
     *
     * @param \Domain\BusinessBundle\Entity\Brand $brand
     *
     * @return BusinessProfile
     */
    public function addBrand(\Domain\BusinessBundle\Entity\Brand $brand)
    {
        $this->brands[] = $brand;

        return $this;
    }

    /**
     * Remove brand
     *
     * @param \Domain\BusinessBundle\Entity\Brand $brand
     */
    public function removeBrand(\Domain\BusinessBundle\Entity\Brand $brand)
    {
        $this->brands->removeElement($brand);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Add paymentMethod
     *
     * @param \Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return BusinessProfile
     */
    public function addPaymentMethod(\Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods[] = $paymentMethod;

        return $this;
    }

    /**
     * Remove paymentMethod
     *
     * @param \Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod
     */
    public function removePaymentMethod(\Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods->removeElement($paymentMethod);
    }

    /**
     * Get paymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * Add task
     *
     * @param \Domain\BusinessBundle\Entity\Task\Task $task
     *
     * @return BusinessProfile
     */
    public function addTask(\Domain\BusinessBundle\Entity\Task\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \Domain\BusinessBundle\Entity\Task\Task $task
     */
    public function removeTask(\Domain\BusinessBundle\Entity\Task\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add businessReview
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview
     *
     * @return BusinessProfile
     */
    public function addBusinessReview(\Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview)
    {
        $this->businessReviews[] = $businessReview;
        $businessReview->setBusinessProfile($this);

        return $this;
    }

    /**
     * Remove businessReview
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview
     */
    public function removeBusinessReview(\Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview)
    {
        $this->businessReviews->removeElement($businessReview);
    }

    /**
     * Get businessReviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessReviews()
    {
        return $this->businessReviews;
    }

    /**
     * Set logo
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\Media $logo
     *
     * @return BusinessProfile
     */
    public function setLogo(\Oxa\Sonata\MediaBundle\Entity\Media $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \Oxa\Sonata\MediaBundle\Entity\Media
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Add image
     *
     * @param \Domain\BusinessBundle\Entity\Media\BusinessGallery $image
     *
     * @return BusinessProfile
     */
    public function addImage(\Domain\BusinessBundle\Entity\Media\BusinessGallery $image)
    {
        $this->images[] = $image;
        $image->setBusinessProfile($this);

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Domain\BusinessBundle\Entity\Media\BusinessGallery $image
     */
    public function removeImage(\Domain\BusinessBundle\Entity\Media\BusinessGallery $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BusinessProfile
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
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set streetAddress
     *
     * @param string $streetAddress
     *
     * @return BusinessProfile
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set fullAddress
     *
     * @param string $fullAddress
     *
     * @return BusinessProfile
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = $fullAddress;

        return $this;
    }

    /**
     * Get fullAddress
     *
     * @return string
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return BusinessProfile
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return BusinessProfile
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return BusinessProfile
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return BusinessProfile
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return BusinessProfile
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set customAddress
     *
     * @param string $customAddress
     *
     * @return BusinessProfile
     */
    public function setCustomAddress($customAddress)
    {
        $this->customAddress = $customAddress;

        return $this;
    }

    /**
     * Get customAddress
     *
     * @return string
     */
    public function getCustomAddress()
    {
        return $this->customAddress;
    }

    /**
     * Set hideAddress
     *
     * @param boolean $hideAddress
     *
     * @return BusinessProfile
     */
    public function setHideAddress($hideAddress)
    {
        $this->hideAddress = $hideAddress;

        return $this;
    }

    /**
     * Get hideAddress
     *
     * @return boolean
     */
    public function getHideAddress()
    {
        return $this->hideAddress;
    }

    /**
     * Set extendedAddress
     *
     * @param string $extendedAddress
     *
     * @return BusinessProfile
     */
    public function setExtendedAddress($extendedAddress)
    {
        $this->extendedAddress = $extendedAddress;

        return $this;
    }

    /**
     * Get extendedAddress
     *
     * @return string
     */
    public function getExtendedAddress()
    {
        return $this->extendedAddress;
    }

    /**
     * Set country
     *
     * @param \Domain\BusinessBundle\Entity\Address\Country $country
     *
     * @return BusinessProfile
     */
    public function setCountry(\Domain\BusinessBundle\Entity\Address\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Domain\BusinessBundle\Entity\Address\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set streetNumber
     *
     * @param string $streetNumber
     *
     * @return BusinessProfile
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * Set useMapAddress
     *
     * @param boolean $useMapAddress
     *
     * @return BusinessProfile
     */
    public function setUseMapAddress($useMapAddress)
    {
        $this->useMapAddress = $useMapAddress;

        return $this;
    }

    /**
     * Get useMapAddress
     *
     * @return boolean
     */
    public function getUseMapAddress()
    {
        return $this->useMapAddress;
    }

    /**
     * Add subscription
     *
     * @param \Domain\BusinessBundle\Entity\Subscription $subscription
     *
     * @return BusinessProfile
     */
    public function addSubscription(\Domain\BusinessBundle\Entity\Subscription $subscription)
    {
        $this->subscriptions[] = $subscription;

        $subscription->setBusinessProfile($this);

        return $this;
    }

    /**
     * Remove subscription
     *
     * @param \Domain\BusinessBundle\Entity\Subscription $subscription
     */
    public function removeSubscription(\Domain\BusinessBundle\Entity\Subscription $subscription)
    {
        $this->subscriptions->removeElement($subscription);
    }

    /**
     * Get subscriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @return Subscription|null
     */
    public function getSubscription()
    {
        $result = null;

        foreach ($this->getSubscriptions() as $subscription) {
            /** @var $subscription Subscription */
            if ($subscription->getStatus() == StatusInterface::STATUS_ACTIVE) {
                $result = $subscription;
            }
        }

        return $result;
    }

    /**
     * @return SubscriptionPlan|null
     */
    public function getSubscriptionPlan()
    {
        $result = null;

        if ($subscription = $this->getSubscription()) {
            $result = $subscription->getSubscriptionPlan();
        }

        return $result;
    }

    /**
     * @return Discount|null
     */
    public function getDiscount()
    {
        $result = null;

        foreach ($this->getDiscounts() as $discount) {
            /** @var $discount Discount */
            if ($discount->getStatus() == StatusInterface::STATUS_ACTIVE) {
                $result = $discount;
            }
        }

        return $result;
    }

    /**
     * Add discount
     *
     * @param \Domain\BusinessBundle\Entity\Discount $discount
     *
     * @return BusinessProfile
     */
    public function addDiscount(\Domain\BusinessBundle\Entity\Discount $discount)
    {
        $this->discounts[] = $discount;

        $discount->setBusinessProfile($this);

        return $this;
    }

    /**
     * Remove discount
     *
     * @param \Domain\BusinessBundle\Entity\Discount $discount
     */
    public function removeDiscount(\Domain\BusinessBundle\Entity\Discount $discount)
    {
        $this->discounts->removeElement($discount);
    }

    /**
     * Get discounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Set searchFts
     *
     * @param tsvector $searchFts
     *
     * @return BusinessProfile
     */
    public function setSearchFts($searchFts)
    {
        $this->searchFts = $searchFts;

        return $this;
    }

    /**
     * Get searchFts
     *
     * @return tsvector
     */
    public function getSearchFts()
    {
        return $this->searchFts;
    }

    /**
     * Set searchNameFts
     *
     * @param tsvector $searchNameFts
     *
     * @return BusinessProfile
     */
    public function setSearchNameFts($searchNameFts)
    {
        $this->searchNameFts = $searchNameFts;

        return $this;
    }

    /**
     * Get searchNameFts
     *
     * @return tsvector
     */
    public function getSearchNameFts()
    {
        return $this->searchNameFts;
    }
}
