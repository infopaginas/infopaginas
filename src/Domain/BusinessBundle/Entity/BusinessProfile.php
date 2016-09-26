<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Domain\BannerBundle\Entity\Campaign;
use Domain\BusinessBundle\Entity\Address\Country;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\ReportBundle\Entity\SearchLog;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\Sonata\UserBundle\Entity\User;
use Domain\SiteBundle\Utils\Traits\SeoTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;
use Symfony\Component\HttpFoundation\File\File;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;

use Oxa\GeolocationBundle\Utils\Traits\LocationTrait;
use Symfony\Component\Validator\Exception\ValidatorException;

use Symfony\Component\Validator\Constraints as Assert;
use Domain\SiteBundle\Validator\Constraints as DomainAssert;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="business_profile")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation")
 */
class BusinessProfile implements
    DefaultEntityInterface,
    CopyableEntityInterface,
    TranslatableInterface,
    GeolocationInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use LocationTrait;
    use SeoTrait;

    const SERVICE_AREAS_AREA_CHOICE_VALUE = 'area';

    const DEFAULT_LOCALE = 'en_US';

    const MILE_TO_KILOMETER  = 0.621371;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - Business name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
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
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="discount", type="text", length=1000, nullable=true)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     */
    protected $discount;

    /**
     * @var Coupon[] - Business Discounts
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Coupon",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     */
    protected $coupons;

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
     * @Assert\Count(min = 1, minMessage = "At least 1 category should be selected")
     */
    protected $categories;

    /**
     * @var string - Website
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     * @DomainAssert\ConstraintUrlExpanded()
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $website;

    /**
     * @var string - Email address
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email()
     * @DomainAssert\ContainsEmailExpanded()
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $email;

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
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="slogan", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
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
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     */
    protected $description;

    /**
     * @var string - Products of Business
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="product", type="text", length=1000, nullable=true)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     */
    protected $product;

    /**
     * @var string - Operational Hours
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="working_hours", type="text", nullable=true)
     */
    protected $workingHours;

    /**
     * @var string Brands - Brands, Business works with
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="brands", type="text", nullable=true)
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
     * @var string - Field is checked, if Video field of profile is set.
     *
     * @ORM\Column(name="is_set_video", type="boolean", options={"default" : 0})
     */
    protected $isSetVideo = false;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var Task[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Task", mappedBy="businessProfile")
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
     * @var string
     *
     * @ORM\Column(name="closure_reason", type="string", length=255, nullable=true)
     */
    protected $closureReason;

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
     *     inversedBy="businessProfiles",
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
     * @ORM\Column(name="street_address", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $streetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="extended_address", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $extendedAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="cross_street", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $crossStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="google_address", type="string", nullable=true)
     */
    protected $googleAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=30, nullable=true)
     * @Assert\Length(max=30, maxMessage="business_profile.max_length")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=10, maxMessage="business_profile.max_length")
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_address", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $customAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="use_map_address", type="boolean", options={"default" : 0})
     */
    protected $useMapAddress = false;

    /**
     * @var string - If checkbox is checked, both address of Business and mark on map are not shown to Consumer.
     *
     * @ORM\Column(name="hide_address", type="boolean", options={"default" : 0})
     */
    protected $hideAddress = false;

    /**
     * @ORM\Column(name="twitter_url", type="string", nullable=true, length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded()
     */
    protected $twitterURL;

    /**
     * @ORM\Column(name="facebook_url", type="string", nullable=true, length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded()
     */
    protected $facebookURL;

    /**
     * @ORM\Column(name="google_url", type="string", nullable=true, length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded()
     */
    protected $googleURL;

    /**
     * @ORM\Column(name="youtube_url", type="string", nullable=true, length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded()
     */
    protected $youtubeURL;

    /**
     * @var Country - Country, Business is located in
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Address\Country",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="service_areas_type", type="string", options={"default": "area"})
     * @Assert\Choice(choices = {"area","locality"}, multiple = false, message = "You must choose a valid Service Area")
     */
    protected $serviceAreasType = 'area';

    /**
     * @var string
     *
     * @ORM\Column(name="miles_of_my_business", type="integer", nullable=true)
     * @Assert\NotBlank(groups={"service_area_chosen"})
     * @Assert\Length(max=4, maxMessage="business_profile.max_length", groups={"service_area_chosen"})
     * @Assert\GreaterThanOrEqual(value=0, groups={"service_area_chosen"})
     */
    protected $milesOfMyBusiness = 100;

    /**
     * @var Locality[] - Using this field a User may define Localities, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Locality",
     *     inversedBy="businessProfile",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_localities")
     */
    protected $localities;

    /**
     * @var Campaign[] - Business Profile Phones
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfilePhone",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $phones;


    /**
     * @ORM\Column(name="uid", type="string")
     */
    protected $uid;

    /**
     * @ORM\ManyToOne(targetEntity="Oxa\WistiaBundle\Entity\WistiaMedia", cascade={"persist"})
     */
    protected $video;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    protected $locale;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_closed", type="boolean", options={"default" : 0})
     */
    protected $isClosed;

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

    /**
     * @var BusinessProfile
     *
     * @ORM\OneToOne(targetEntity="Oxa\DfpBundle\Entity\DoubleClickSynchLog",
     *     mappedBy="businessProfile",
     *     cascade={"persist"}
     * )
     */
    private $doubleClickSynchLog;

    /**
     * @var BusinessProfile
     *
     * @ORM\OneToOne(targetEntity="Oxa\DfpBundle\Entity\DoubleClickCompany",
     *     mappedBy="businessProfile",
     *     cascade={"persist"}
     * )
     */
    private $doubleClickCompany;

    /** @var float
     *
     * keeps the distance between user and pusiness. not a part of DB table. calculated during the search
     */
    protected $distance;

     /** @var SearchLog[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\ReportBundle\Entity\SearchLog",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $searchLogs;

    /**
     * @return mixed
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param mixed $video
     * @return BusinessProfile
     */
    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    public function __toString()
    {
        return $this->getName() ?: '';
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
        $this->coupons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subscriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->businessReviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->searchLogs = new \Doctrine\Common\Collections\ArrayCollection();

        $this->isClosed = false;

        $this->uid = uniqid('', true);
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
     * Get website final link
     *
     * @return string
     */
    public function getWebsiteLink()
    {
        if (preg_match('/^http/', $this->getWebsite())) {
            return $this->getWebsite();
        }

        return '//' . $this->getWebsite();
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
     * @return string
     */
    public function getIsSetVideo()
    {
        return $this->isSetVideo;
    }

    /**
     * @param string $isSetVideo
     * @return BusinessProfile
     */
    public function setIsSetVideo($isSetVideo)
    {
        $this->isSetVideo = $isSetVideo;

        return $this;
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
     * Get brands
     *
     * @return string
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param string $brands
     * @return BusinessProfile
     */
    public function setBrands($brands)
    {
        $this->brands = $brands;

        return $this;
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
     * @param \Domain\BusinessBundle\Entity\Task $task
     *
     * @return BusinessProfile
     */
    public function addTask(\Domain\BusinessBundle\Entity\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \Domain\BusinessBundle\Entity\Task $task
     */
    public function removeTask(\Domain\BusinessBundle\Entity\Task $task)
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
     * @return string
     */
    public function getClosureReason()
    {
        return $this->closureReason;
    }

    /**
     * @param string $closureReason
     * @return BusinessProfile
     */
    public function setClosureReason($closureReason)
    {
        $this->closureReason = $closureReason;
        return $this;
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

        if ($image->getType() == OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO) {
            $this->setLogo($image->getMedia());
        }

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
     * @return string
     */
    public function getCrossStreet()
    {
        return $this->crossStreet;
    }

    /**
     * @param string $crossStreet
     * @return BusinessProfile
     */
    public function setCrossStreet($crossStreet)
    {
        $this->crossStreet = $crossStreet;

        return $this;
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
     * @return mixed
     */
    public function getTwitterURL()
    {
        return $this->twitterURL;
    }

    /**
     * @param mixed $twitterURL
     * @return User
     */
    public function setTwitterURL($twitterURL)
    {
        $this->twitterURL = $twitterURL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFacebookURL()
    {
        return $this->facebookURL;
    }

    /**
     * @param mixed $facebookURL
     * @return User
     */
    public function setFacebookURL($facebookURL)
    {
        $this->facebookURL = $facebookURL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleURL()
    {
        return $this->googleURL;
    }

    /**
     * @param mixed $googleURL
     * @return User
     */
    public function setGoogleURL($googleURL)
    {
        $this->googleURL = $googleURL;
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
        $entitiesCollection = $this->getSubscriptions()->filter(
            function (StatusInterface $object) {
                return ($object->getStatus() == StatusInterface::STATUS_ACTIVE);
            }
        );

        return $entitiesCollection->first() ?: null;
    }

    /**
     * @return SubscriptionPlan|null
     */
    public function getSubscriptionPlan()
    {
        return $this->getSubscription() ? $this->getSubscription()->getSubscriptionPlan() : null;
    }

    /**
     * @return mixed
     */
    public function getYoutubeURL()
    {
        return $this->youtubeURL;
    }

    /**
     * @param mixed $youtubeURL
     * @return User
     */
    public function setYoutubeURL($youtubeURL)
    {
        $this->youtubeURL = $youtubeURL;
        return $this;
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
     * @return BusinessProfile
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
     * @return BusinessProfile
     */
    public function setMilesOfMyBusiness($milesOfMyBusiness)
    {
        $this->milesOfMyBusiness = $milesOfMyBusiness;
        return $this;
    }

    /**
     * @return Locality[]
     */
    public function getLocalities()
    {
        return $this->localities;
    }

    /**
     * @param Locality[] $localities
     * @return BusinessProfile
     */
    public function setLocalities($localities)
    {
        $this->localities = $localities;
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

    /*
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

    /**
     * @return mixed
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set googleAddress
     *
     * @param string $googleAddress
     *
     * @return BusinessProfile
     */
    public function setGoogleAddress($googleAddress)
    {
        $this->googleAddress = $googleAddress;

        return $this;
    }

    /**
     * Get googleAddress
     *
     * @return string
     */
    public function getGoogleAddress()
    {
        return $this->googleAddress;
    }

    /**
     * Set searchCityFts
     *
     * @param tsvector $searchCityFts
     *
     * @return BusinessProfile
     */
    public function setSearchCityFts($searchCityFts)
    {
        $this->searchCityFts = $searchCityFts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     * @return BusinessProfile
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * Get searchCityFts
     *
     * @return tsvector
     */
    public function getSearchCityFts()
    {
        return $this->searchCityFts;
    }

    /**
     * Add campaign
     *
     * @param \Domain\BannerBundle\Entity\Campaign $campaign
     *
     * @return BusinessProfile
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
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCampaign(\Domain\BannerBundle\Entity\Campaign $campaign)
    {
        return $this->campaigns->removeElement($campaign);
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

    /**
     * Get full address
     * @return string
     */
    public function getFullAddress()
    {
        $address = [];

        if ($this->getCustomAddress()) {
            return $this->getCustomAddress();
        }

        if ($this->getStreetNumber()) {
            $address[] = $this->getStreetNumber();
        }

        if ($this->getStreetAddress()) {
            $address[] = $this->getStreetAddress();
        }

        if ($this->getZipCode()) {
            $address[] = $this->getZipCode();
        }

        if ($this->getCity()) {
            $address[] = $this->getCity();
        }

        if ($this->getState()) {
            $address[] = $this->getState();
        }

        if ($this->getCountry()) {
            $address[] = $this->getCountry()->getName();
        }

        if ($address) {
            $addressResult = implode(', ', $address);
        } else {
            $addressResult = $this->getGoogleAddress();
        }

        return $addressResult;
    }

    /**
     * Single access point to get address
     * @return string
     */
    public function getShortAddress()
    {
        if ($this->getHideAddress()) {
            return '';
        }

        if ($this->getCustomAddress()) {
            return $this->getCustomAddress();
        }

        $address = [];
        if ($this->getStreetAddress()) {
            $address[] = $this->getStreetAddress();
        }

        if ($this->getZipCode()) {
            $address[] = $this->getZipCode();
        }

        if ($this->getCity()) {
            $address[] = $this->getCity();
        }

        if ($address) {
            $addressResult = implode(', ', $address);
        } else {
            $addressResult = $this->getGoogleAddress();
        }

        return $addressResult;
    }

    /*
     * Get count of BusinessProfile reviews
     * @return int
     */
    public function getBusinessReviewsCount()
    {
        return $this->getBusinessReviews()->count();
    }

    /**
     * Get avg mark of BusinessProfile reviews
     * @return int
     */
    public function getBusinessReviewsAvgMark()
    {
        $raiting = 0;
        $reviewsAmount = $this->getBusinessReviewsCount();

        if ($reviewsAmount) {
            foreach ($this->getBusinessReviews() as $review) {
                $raiting += (int) $review->getRating();
            }
            return $raiting / $reviewsAmount;
        }

        return 0;
    }

    /**
     * Add locality
     *
     * @param \Domain\BusinessBundle\Entity\Locality $locality
     *
     * @return BusinessProfile
     */
    public function addLocality(\Domain\BusinessBundle\Entity\Locality $locality)
    {
        $this->localities[] = $locality;

        return $this;
    }

    /**
     * Remove locality
     *
     * @param \Domain\BusinessBundle\Entity\Locality $locality
     */
    public function removeLocality(\Domain\BusinessBundle\Entity\Locality $locality)
    {
        $this->localities->removeElement($locality);
    }

    /**
     * Add phone
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfilePhone $phone
     *
     * @return BusinessProfile
     */
    public function addPhone($phone)
    {
        $this->phones[] = $phone;

        if ($phone) {
            $phone->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove phone
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfilePhone $phone
     */
    public function removePhone(\Domain\BusinessBundle\Entity\BusinessProfilePhone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return BusinessProfile
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Add coupon
     *
     * @param \Domain\BusinessBundle\Entity\Coupon $coupon
     *
     * @return BusinessProfile
     */
    public function addCoupon(\Domain\BusinessBundle\Entity\Coupon $coupon)
    {
        $this->coupons[] = $coupon;

        $coupon->setBusinessProfile($this);

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \Domain\BusinessBundle\Entity\Coupon $coupon
     */
    public function removeCoupon(\Domain\BusinessBundle\Entity\Coupon $coupon)
    {
        $this->coupons->removeElement($coupon);
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return boolean
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * @param boolean $isClosed
     * @return BusinessProfile
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCitySlug()
    {
        // todo - replace with Gedmo\Sluggable\Util\Urlizer

        $citySlug = str_replace(' ', '-', preg_replace('/[^a-z\d ]/i', '', strtolower($this->getCity())));

        return $citySlug;
    }

    /**
     * @return BusinessProfile
     */
    public function getDoubleClickSynchLog()
    {
        return $this->doubleClickSynchLog;
    }

    /**
     * @param BusinessProfile $doubleClickSynchLog
     * @return BusinessProfile
     */
    public function setDoubleClickSynchLog($doubleClickSynchLog)
    {
        $this->doubleClickSynchLog = $doubleClickSynchLog;
    }

    /** getting distance
     *
     * @return float
     */
    public function getDistance() : float
    {
        return $this->distance;
    }

    /**
     * Setting distance
     *
     * @param float $distance
     * @return this
     */
    public function setDistance(float $distance)
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return BusinessProfile
     */
    public function getDoubleClickCompany()
    {
        return $this->doubleClickCompany;
    }

    /**
     * @param BusinessProfile $doubleClickCompany
     * @return BusinessProfile
     */
    public function setDoubleClickCompany($doubleClickCompany)
    {
        $this->doubleClickCompany = $doubleClickCompany;
    }

     /**
     * getting distance prettified
     *
     * @return string
     */
    public function getDistanceUX() : string
    {
        // convert to miles
        $miles = $this->getDistance() * self::MILE_TO_KILOMETER;

        return number_format($miles, 2, '.', '');
    }

    /**
     * Add searchLog
     *
     * @param \Domain\ReportBundle\Entity\SearchLog $searchLog
     * @return BusinessProfile
     */
    public function addSearchLog(\Domain\ReportBundle\Entity\SearchLog $searchLog)
    {
        $this->searchLogs[] = $searchLog;
        return $this;
    }

    /**
     * @return string
     */
    public function getDoubleClickExternalId()
    {
        return $this->getUser() ? $this->getUser()->getAdvertiserId() : '';
    }

    /** Remove searchLog
     *
     * @param \Domain\ReportBundle\Entity\SearchLog $searchLog
     */
    public function removeSearchLog(\Domain\ReportBundle\Entity\SearchLog $searchLog)
    {
        $this->searchLogs->removeElement($searchLog);
    }

    /**
     * Get searchLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSearchLogs()
    {
        return $this->searchLogs;
    }

    public static function getServiceAreasTypes()
    {
        return [
            'area' => 'Distance',
            'locality' => 'Locality'
        ];
    }
}
