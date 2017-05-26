<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Address\Country;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
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
    const SERVICE_AREAS_LOCALITY_CHOICE_VALUE = 'locality';

    const BUSINESS_PROFILE_FIELD_NAME_LENGTH          = 255;
    const BUSINESS_PROFILE_FIELD_DESCRIPTION_LENGTH   = 10000;
    const BUSINESS_PROFILE_FIELD_PRODUCT_LENGTH       = 10000;
    const BUSINESS_PROFILE_FIELD_BRANDS_LENGTH        = 1024;
    const BUSINESS_PROFILE_FIELD_WORKING_HOURS_LENGTH = 255;
    const BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH        = 255;

    const BUSINESS_STATUS_ACTIVE   = 'active';
    const BUSINESS_STATUS_INACTIVE = 'inactive';

    const BUSINESS_PROFILE_ZIP_MAX_LENGTH = 10;
    const BUSINESS_PROFILE_URL_MAX_LENGTH = 1000;

    const DEFAULT_LOCALE = 'en';

    const TRANSLATION_LANG_EN = 'En';
    const TRANSLATION_LANG_ES = 'Es';

    const ELASTIC_DOCUMENT_TYPE = 'BusinessProfile';
    const FLAG_IS_UPDATED = 'isUpdated';

    const DEFAULT_MILES_FROM_MY_BUSINESS = 0;
    const DISTANCE_TO_BUSINESS_PRECISION = 1;

    // translatable fields
    const BUSINESS_PROFILE_FIELD_NAME           = 'name';
    const BUSINESS_PROFILE_FIELD_NAME_EN        = 'nameEn';
    const BUSINESS_PROFILE_FIELD_NAME_ES        = 'nameEs';
    const BUSINESS_PROFILE_FIELD_DESCRIPTION    = 'description';
    const BUSINESS_PROFILE_FIELD_DESCRIPTION_EN = 'descriptionEn';
    const BUSINESS_PROFILE_FIELD_DESCRIPTION_ES = 'descriptionEs';
    const BUSINESS_PROFILE_FIELD_PRODUCT        = 'product';
    const BUSINESS_PROFILE_FIELD_BRANDS         = 'brands';
    const BUSINESS_PROFILE_FIELD_WORKING_HOURS  = 'workingHours';
    const BUSINESS_PROFILE_FIELD_SLOGAN         = 'slogan';

    // common fields
    const BUSINESS_PROFILE_FIELD_WEBSITE    = 'website';
    const BUSINESS_PROFILE_FIELD_EMAIL      = 'email';

    const BUSINESS_PROFILE_FIELD_SERVICE_AREAS_TYPE     = 'serviceAreasType';
    const BUSINESS_PROFILE_FIELD_MILES_OF_MY_BUSINESS   = 'milesOfMyBusiness';

    const BUSINESS_PROFILE_FIELD_STREET_ADDRESS     = 'streetAddress';
    const BUSINESS_PROFILE_FIELD_STREET_NUMBER      = 'streetNumber';
    const BUSINESS_PROFILE_FIELD_EXTENDED_ADDRESS   = 'extendedAddress';
    const BUSINESS_PROFILE_FIELD_CROSS_STREET       = 'crossStreet';
    const BUSINESS_PROFILE_FIELD_GOOGLE_ADDRESS     = 'googleAddress';
    const BUSINESS_PROFILE_FIELD_STATE              = 'state';
    const BUSINESS_PROFILE_FIELD_CITY               = 'city';
    const BUSINESS_PROFILE_FIELD_ZIP_CODE           = 'zipCode';
    const BUSINESS_PROFILE_FIELD_CUSTOM_ADDRESS     = 'customAddress';
    const BUSINESS_PROFILE_FIELD_USE_MAP_ADDRESS    = 'useMapAddress';
    const BUSINESS_PROFILE_FIELD_HIDE_ADDRESS       = 'hideAddress';

    const BUSINESS_PROFILE_FIELD_TWITTER_URL    = 'twitterURL';
    const BUSINESS_PROFILE_FIELD_FACEBOOK_URL   = 'facebookURL';
    const BUSINESS_PROFILE_FIELD_GOOGLE_URL     = 'googleURL';
    const BUSINESS_PROFILE_FIELD_YOUTUBE_URL    = 'youtubeURL';
    const BUSINESS_PROFILE_FIELD_INSTAGRAM_URL  = 'instagramURL';
    const BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL = 'tripAdvisorURL';

    const BUSINESS_PROFILE_FIELD_SEO_TITLE       = 'seoTitle';
    const BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION = 'seoDescription';

    const BUSINESS_PROFILE_FIELD_LATITUDE = 'latitude';
    const BUSINESS_PROFILE_FIELD_LONGITUDE = 'longitude';

    // many-to-one relations
    const BUSINESS_PROFILE_FIELD_CATALOG_LOCALITY = 'catalogLocality';
    const BUSINESS_PROFILE_FIELD_COUNTRY          = 'country';

    // one-to-many relations
    const BUSINESS_PROFILE_RELATION_WORKING_HOURS   = 'collectionWorkingHours';
    const BUSINESS_PROFILE_RELATION_PHONES          = 'phones';

    const WORKING_HOURS_ASSOCIATED_FIELD = 'collectionWorkingHours';

    // many-to-many relations
    const BUSINESS_PROFILE_RELATION_CATEGORIES      = 'categories';
    const BUSINESS_PROFILE_RELATION_AREAS           = 'areas';
    const BUSINESS_PROFILE_RELATION_PAYMENT_METHODS = 'paymentMethods';
    const BUSINESS_PROFILE_RELATION_LOCALITIES      = 'localities';
    const BUSINESS_PROFILE_RELATION_NEIGHBORHOODS   = 'neighborhoods';

    // one-to-many media relations
    const BUSINESS_PROFILE_RELATION_IMAGES   = 'images';

    // many-to-one media relations
    const BUSINESS_PROFILE_RELATION_VIDEO      = 'video';
    const BUSINESS_PROFILE_RELATION_LOGO       = 'logo';
    const BUSINESS_PROFILE_RELATION_BACKGROUND = 'background';

    const BUSINESS_PROFILE_FIELD_LOGO       = 'logo';
    const BUSINESS_PROFILE_FIELD_BACKGROUND = 'background';

    const BUSINESS_PROFILE_RELATION_TRANSLATIONS = 'translations';

    const BUSINESS_PROFILE_FIELD_SUBSCRIPTIONS    = 'subscriptions';
    const BUSINESS_PROFILE_FIELD_UPDATED_AT       = 'updatedAt';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Field related to class constant BUSINESS_PROFILE_FIELD_NAME_LENGTH
     * Field related to class constant BUSINESS_PROFILE_FIELD_NAME
     * @var string - Business name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $name;

    /**
     * @var string - Business name en
     *
     * @ORM\Column(name="name_en", type="string", length=255, nullable=true)
     */
    protected $nameEn;

    /**
     * @var string - Business name es
     *
     * @ORM\Column(name="name_es", type="string", length=255, nullable=true)
     */
    protected $nameEs;

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
     * @var Category[] - Business category
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Category",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"},
     *     orphanRemoval=false
     *     )
     * @ORM\JoinTable(name="business_profile_categories")
     * @Assert\Count(min = 1, minMessage = "business_profile.category.min_count", groups={"default"})
     */
    protected $categories;

    /**
     * Related to BUSINESS_PROFILE_URL_MAX_LENGTH
     * @var string - Website
     *
     * @ORM\Column(name="website", type="string", length=1000, nullable=true)
     * @DomainAssert\ConstraintUrlExpanded()
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
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
     * Field related to class constant BUSINESS_PROFILE_FIELD_SLOGAN
     * Field related to class constant BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH
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
     * Field related to class constant BUSINESS_PROFILE_FIELD_DESCRIPTION
     * Field related to class constant BUSINESS_PROFILE_FIELD_DESCRIPTION_LENGTH
     * @var string - Description of Business
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", length=10000, nullable=true)
     * @Assert\Length(max=10000, maxMessage="business_profile.max_length")
     */
    protected $description;

    /**
     * @var string - Description of Business en
     *
     * @ORM\Column(name="description_en", type="text", length=10000, nullable=true)
     */
    protected $descriptionEn;

    /**
     * @var string - Description of Business es
     *
     * @ORM\Column(name="description_es", type="text", length=10000, nullable=true)
     */
    protected $descriptionEs;

    /**
     * Field related to class constant BUSINESS_PROFILE_FIELD_PRODUCT
     * Field related to class constant BUSINESS_PROFILE_FIELD_PRODUCT_LENGTH
     * @var string - Products of Business
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="product", type="text", length=10000, nullable=true)
     * @Assert\Length(max=10000, maxMessage="business_profile.max_length")
     */
    protected $product;

    /**
     * Field related to class constant BUSINESS_PROFILE_FIELD_WORKING_HOURS
     * Field related to class constant BUSINESS_PROFILE_FIELD_WORKING_HOURS_LENGTH
     * @var string - Operational Hours
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="working_hours", type="text", nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $workingHours;

    /**
     * Field related to class constant BUSINESS_PROFILE_FIELD_BRANDS
     * Field related to class constant BUSINESS_PROFILE_FIELD_WORKING_HOURS_LENGTH
     * @var string Brands - Brands, Business works with
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="brands", type="text", nullable=true)
     * @Assert\Length(max=1024, maxMessage="business_profile.max_length")
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
     * Logo Field. Related to class constant BUSINESS_PROFILE_FIELD_LOGO
     *
     * @var Media - Media Logo
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="logoBusinessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    protected $logo;

    /**
     * Background Field. Related to class constant BUSINESS_PROFILE_FIELD_BACKGROUND
     *
     * @var Media - Media Background Image
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="backgroundBusinessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="background_id", referencedColumnName="id", nullable=true)
     */
    protected $background;

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
     * Related to const BUSINESS_PROFILE_ZIP_MAX_LENGTH
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
     * Related to BUSINESS_PROFILE_URL_MAX_LENGTH
     * @ORM\Column(name="twitter_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded(groups={"default"})
     */
    protected $twitterURL;

    /**
     * Related to BUSINESS_PROFILE_URL_MAX_LENGTH
     * @ORM\Column(name="facebook_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded(groups={"default"})
     */
    protected $facebookURL;

    /**
     * Related to BUSINESS_PROFILE_URL_MAX_LENGTH
     * @ORM\Column(name="google_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded(groups={"default"})
     */
    protected $googleURL;

    /**
     * Related to BUSINESS_PROFILE_URL_MAX_LENGTH
     * @ORM\Column(name="youtube_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded(groups={"default"})
     */
    protected $youtubeURL;

    /**
     * Related to BUSINESS_PROFILE_FIELD_INSTAGRAM_URL
     * @ORM\Column(name="instagram_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded(groups={"default"})
     */
    protected $instagramURL;

    /**
     * Related to BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL
     * @ORM\Column(name="trip_advisor_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     * @DomainAssert\ConstraintUrlExpanded(groups={"default"})
     */
    protected $tripAdvisorURL;

    /**
     * Field related to const BUSINESS_PROFILE_FIELD_COUNTRY
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
     * @Assert\Choice(choices = {"area","locality"}, multiple = false, message = "business_profile.service_areas_type")
     */
    protected $serviceAreasType = 'area';

    /**
     * @var string
     *
     * @ORM\Column(name="miles_of_my_business", type="integer", nullable=true)
     * @Assert\NotBlank(groups={"service_area_chosen"})
     * @Assert\Type(type="digit", message="business_profile.integer_miles", groups={"service_area_chosen"})
     * @Assert\Length(max=4, maxMessage="business_profile.max_length", groups={"service_area_chosen"})
     */
    protected $milesOfMyBusiness;

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
     * @var Neighborhood[] - Using this field a User may define Neighborhoods, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Neighborhood",
     *     inversedBy="businessProfile",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_neighborhoods")
     */
    protected $neighborhoods;

    /**
     * @var BusinessProfilePhone[] - Business Profile Phones
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
     * @ORM\ManyToOne(targetEntity="Oxa\VideoBundle\Entity\VideoMedia",
     *     inversedBy="businessProfiles"
     * )
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", nullable=true)
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

    /** @var float
     *
     * keeps the distance between user and pusiness. not a part of DB table. calculated during the search
     */
    protected $distance;

    /**
     * Related to WORKING_HOURS_ASSOCIATED_FIELD
     * @var BusinessProfileWorkingHour[] - Business Profile working hours
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileWorkingHour",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $collectionWorkingHours;

    /**
     * @var string
     *
     * @ORM\Column(name="working_hours_json", type="text", nullable=true)
     */
    protected $workingHoursJson;

    /**
     * @var int - Subscription code
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="subscription_plan_code", type="integer", nullable=true,  options={"default" : 1})
     */
    protected $subscriptionPlanCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_images", type="boolean", options={"default" : 0})
     */
    protected $hasImages;

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

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop)
    {
        return isset($this->$prop);
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
     * Field related to const BUSINESS_PROFILE_FIELD_CATALOG_LOCALITY
     * @var $catalogLocality - catalogLocality, Business is located in
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Locality",
     *     inversedBy="businessProfiles",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $catalogLocality;

    /**
     * Related to FLAG_IS_UPDATED const
     * @var bool
     *
     * @ORM\Column(name="is_updated", type="boolean", options={"default" : 1})
     */
    protected $isUpdated;

    /**
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @ORM\Column(name="dc_order_id", type="string", nullable=true, length=255)
     */
    protected $dcOrderId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->coupons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subscriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->localities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->neighborhoods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->businessReviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->collectionWorkingHours = new \Doctrine\Common\Collections\ArrayCollection();

        $this->isClosed  = false;
        $this->isUpdated = true;
        $this->hasImages = false;
        $this->milesOfMyBusiness = self::DEFAULT_MILES_FROM_MY_BUSINESS;

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
     * Set nameEn
     *
     * @param string $nameEn
     *
     * @return BusinessProfile
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Set nameEs
     *
     * @param string $nameEs
     *
     * @return BusinessProfile
     */
    public function setNameEs($nameEs)
    {
        $this->nameEs = $nameEs;

        return $this;
    }

    /**
     * Get nameEs
     *
     * @return string
     */
    public function getNameEs()
    {
        return $this->nameEs;
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
        $http = 'http';

        if (preg_match('/^' . $http . '/', $this->getWebsite())) {
            return $this->getWebsite();
        }

        return $http . '://' . $this->getWebsite();
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
     * Set descriptionEn
     *
     * @param string $descriptionEn
     *
     * @return BusinessProfile
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * Get descriptionEn
     *
     * @return string
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * Set descriptionEs
     *
     * @param string $descriptionEs
     *
     * @return BusinessProfile
     */
    public function setDescriptionEs($descriptionEs)
    {
        $this->descriptionEs = $descriptionEs;

        return $this;
    }

    /**
     * Get descriptionEs
     *
     * @return string
     */
    public function getDescriptionEs()
    {
        return $this->descriptionEs;
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
    public function setLogo($logo = null)
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
     * Set background
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\Media $background
     *
     * @return BusinessProfile
     */
    public function setBackground($background = null)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Get background
     *
     * @return \Oxa\Sonata\MediaBundle\Entity\Media
     */
    public function getBackground()
    {
        return $this->background;
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
     * @return mixed
     */
    public function getInstagramURL()
    {
        return $this->instagramURL;
    }

    /**
     * @param mixed $instagramURL
     *
     * @return User
     */
    public function setInstagramURL($instagramURL)
    {
        $this->instagramURL = $instagramURL;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTripAdvisorURL()
    {
        return $this->tripAdvisorURL;
    }

    /**
     * @param mixed $tripAdvisorURL
     *
     * @return User
     */
    public function setTripAdvisorURL($tripAdvisorURL)
    {
        $this->tripAdvisorURL = $tripAdvisorURL;

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
     * @return ArrayCollection
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
     * @return ArrayCollection
     */
    public function getNeighborhoods()
    {
        return $this->neighborhoods;
    }

    /**
     * @param Neighborhood[] $neighborhoods
     * @return BusinessProfile
     */
    public function setNeighborhoods($neighborhoods)
    {
        $this->neighborhoods = $neighborhoods;
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
        $this->isUpdated = true;
        $this->createdAt = null;
        $this->updatedAt = null;
        $this->updatedUser = null;
        $this->createdUser = null;
        $this->businessReviews = new ArrayCollection();
        $this->video = null;

        $this->translations = new ArrayCollection();
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
     * Add Neighborhood
     *
     * @param \Domain\BusinessBundle\Entity\Neighborhood $neighborhood
     *
     * @return BusinessProfile
     */
    public function addNeighborhood(\Domain\BusinessBundle\Entity\Neighborhood $neighborhood)
    {
        $this->neighborhoods[] = $neighborhood;

        return $this;
    }

    /**
     * Remove Neighborhood
     *
     * @param \Domain\BusinessBundle\Entity\Neighborhood $neighborhood
     */
    public function removeNeighborhood(\Domain\BusinessBundle\Entity\Neighborhood $neighborhood)
    {
        $this->neighborhoods->removeElement($neighborhood);
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
     * getting distance prettified
     *
     * @return string
     */
    public function getDistanceUX() : string
    {
        return number_format($this->getDistance(), self::DISTANCE_TO_BUSINESS_PRECISION, '.', '');
    }

    /**
     * @return string
     */
    public function getDoubleClickExternalId()
    {
        return $this->getUser() ? $this->getUser()->getAdvertiserId() : '';
    }

    public static function getServiceAreasTypes()
    {
        return [
            self::SERVICE_AREAS_AREA_CHOICE_VALUE       => 'Distance',
            self::SERVICE_AREAS_LOCALITY_CHOICE_VALUE   => 'Locality'
        ];
    }

    /**
     * Set catalogLocality
     *
     * @param \Domain\BusinessBundle\Entity\Locality $catalogLocality
     *
     * @return BusinessProfile
     */
    public function setCatalogLocality(\Domain\BusinessBundle\Entity\Locality $catalogLocality = null)
    {
        $this->catalogLocality = $catalogLocality;

        return $this;
    }

    /**
     * Get catalogLocality
     *
     * @return \Domain\BusinessBundle\Entity\Locality
     */
    public function getCatalogLocality()
    {
        return $this->catalogLocality;
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
     * @return string
     */
    public function getDcOrderId()
    {
        return $this->dcOrderId;
    }

    /**
     * @param string $dcOrderId
     *
     * @return BusinessProfile
     */
    public function setDcOrderId($dcOrderId)
    {
        $this->dcOrderId = $dcOrderId;

        return $this;
    }

    public function getActiveStatus()
    {
        return $this->getIsActive() ? self::BUSINESS_STATUS_ACTIVE : self::BUSINESS_STATUS_INACTIVE;
    }

    public function getExportCategories()
    {
        $data = [];

        $categories = $this->getCategories();

        foreach ($categories as $category) {
            $data[] = [
                'id'   => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return json_encode($data);
    }

    /**
     * @return ArrayCollection
     */
    public function getCollectionWorkingHours()
    {
        return $this->collectionWorkingHours;
    }

    /**
     * Add $workingHour
     *
     * @param BusinessProfileWorkingHour $workingHour
     *
     * @return BusinessProfile
     */
    public function addCollectionWorkingHour($workingHour)
    {
        $this->collectionWorkingHours[] = $workingHour;

        if ($workingHour) {
            $workingHour->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $workingHours
     *
     * @param BusinessProfileWorkingHour $workingHours
     */
    public function removeCollectionWorkingHour(BusinessProfileWorkingHour $workingHours)
    {
        $this->collectionWorkingHours->removeElement($workingHours);
    }

    /**
     * get list of bilingual fields
     * @return array
     */
    public static function getTranslatableFields()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_NAME,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION,
            self::BUSINESS_PROFILE_FIELD_PRODUCT,
            self::BUSINESS_PROFILE_FIELD_BRANDS,
            self::BUSINESS_PROFILE_FIELD_WORKING_HOURS,
            self::BUSINESS_PROFILE_FIELD_SLOGAN,
        ];
    }

    public static function getCommonBooleanFields()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_USE_MAP_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_HIDE_ADDRESS,
        ];
    }

    public static function getTaskCommonFields()
    {
        return [
            // translatable field
            self::BUSINESS_PROFILE_FIELD_NAME,
            self::BUSINESS_PROFILE_FIELD_NAME_EN,
            self::BUSINESS_PROFILE_FIELD_NAME_ES,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION_EN,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION_ES,

            self::BUSINESS_PROFILE_FIELD_PRODUCT,
            self::BUSINESS_PROFILE_FIELD_BRANDS,
            self::BUSINESS_PROFILE_FIELD_WORKING_HOURS,

            self::BUSINESS_PROFILE_FIELD_WEBSITE,
            self::BUSINESS_PROFILE_FIELD_EMAIL,

            self::BUSINESS_PROFILE_FIELD_SERVICE_AREAS_TYPE,
            self::BUSINESS_PROFILE_FIELD_MILES_OF_MY_BUSINESS,

            self::BUSINESS_PROFILE_FIELD_STREET_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_STREET_NUMBER,
            self::BUSINESS_PROFILE_FIELD_EXTENDED_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_CROSS_STREET,
            self::BUSINESS_PROFILE_FIELD_GOOGLE_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_STATE,
            self::BUSINESS_PROFILE_FIELD_CITY,
            self::BUSINESS_PROFILE_FIELD_ZIP_CODE,
            self::BUSINESS_PROFILE_FIELD_CUSTOM_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_USE_MAP_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_HIDE_ADDRESS,

            self::BUSINESS_PROFILE_FIELD_TWITTER_URL,
            self::BUSINESS_PROFILE_FIELD_FACEBOOK_URL,
            self::BUSINESS_PROFILE_FIELD_GOOGLE_URL,
            self::BUSINESS_PROFILE_FIELD_YOUTUBE_URL,
            self::BUSINESS_PROFILE_FIELD_INSTAGRAM_URL,
            self::BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL,

            // geo
            self::BUSINESS_PROFILE_FIELD_LATITUDE,
            self::BUSINESS_PROFILE_FIELD_LONGITUDE,
        ];
    }

    public static function getTaskManyToOneRelations()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_CATALOG_LOCALITY,
            self::BUSINESS_PROFILE_FIELD_COUNTRY,
        ];
    }

    public static function getTaskOneToManyRelations()
    {
        return [
            self::BUSINESS_PROFILE_RELATION_WORKING_HOURS,
            self::BUSINESS_PROFILE_RELATION_PHONES,
        ];
    }

    public static function getTaskManyToManyRelations()
    {
        return [
            self::BUSINESS_PROFILE_RELATION_CATEGORIES,
            self::BUSINESS_PROFILE_RELATION_AREAS,
            self::BUSINESS_PROFILE_RELATION_PAYMENT_METHODS,
            self::BUSINESS_PROFILE_RELATION_LOCALITIES,
            self::BUSINESS_PROFILE_RELATION_NEIGHBORHOODS,
        ];
    }

    public static function getTaskMediaManyToOneRelations()
    {
        return [
            self::BUSINESS_PROFILE_RELATION_VIDEO,
            self::BUSINESS_PROFILE_RELATION_LOGO,
            self::BUSINESS_PROFILE_RELATION_BACKGROUND,
        ];
    }

    public static function getTaskMediaOneToManyRelations()
    {
        return [
            self::BUSINESS_PROFILE_RELATION_IMAGES,
        ];
    }

    public static function getTaskSeoBlock()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_SEO_TITLE,
            self::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION,
        ];
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateWorkingHoursData()
    {
        $workingHours = DayOfWeekModel::getBusinessProfileWorkingHoursJson($this);

        $this->workingHoursJson = $workingHours;
    }

    /**
     * @return string
     */
    public function getWorkingHoursJson()
    {
        return $this->workingHoursJson;
    }

    /**
     * @param string $workingHoursJson
     *
     * @return BusinessProfile
     */
    public function setWorkingHoursJson($workingHoursJson)
    {
        $this->workingHoursJson = $workingHoursJson;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getWorkingHoursJsonAsObject()
    {
        return json_decode($this->getWorkingHoursJson());
    }

    /**
     * @return boolean
     */
    public function getHasImages()
    {
        return $this->hasImages;
    }

    /**
     * @param boolean $hasImages
     *
     * @return BusinessProfile
     */
    public function setHasImages($hasImages)
    {
        $this->hasImages = $hasImages;

        return $this;
    }

    /**
     * @return int
     */
    public function getSubscriptionPlanCode()
    {
        return $this->subscriptionPlanCode;
    }

    /**
     * @param int $subscriptionPlanCode
     *
     * @return BusinessProfile
     */
    public function setSubscriptionPlanCode($subscriptionPlanCode)
    {
        $this->subscriptionPlanCode = $subscriptionPlanCode;

        return $this;
    }
}
