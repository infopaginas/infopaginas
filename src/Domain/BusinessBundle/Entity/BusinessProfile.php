<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Entity\Address\Country;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckboxCollection;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListCollection;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonCollection;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldTextAreaCollection;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\ZipFormatterUtil;
use Domain\ReportBundle\Model\PostponeExportInterface;
use Domain\ReportBundle\Model\ReportInterface;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Model\PostponeRemoveInterface;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\PostponeRemoveTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\UserBundle\Entity\User;
use Domain\SiteBundle\Utils\Traits\SeoTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;
use Symfony\Component\HttpFoundation\File\File;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Oxa\GeolocationBundle\Utils\Traits\LocationTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Domain\SiteBundle\Validator\Constraints as DomainAssert;
use Domain\BusinessBundle\Validator\Constraints\ServiceAreaType as ServiceAreaTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfilePhoneType as BusinessProfilePhoneTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourType as BusinessProfileWorkingHourTypeValidator;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileMediaUrlType as BusinessProfileMediaUrlTypeValidator;
use Domain\BusinessBundle\VO\Url;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="business_profile", indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation")
 * @ServiceAreaTypeValidator(groups={"Admin"})
 * @BusinessProfilePhoneTypeValidator()
 * @BusinessProfileWorkingHourTypeValidator()
 * @BusinessProfileMediaUrlTypeValidator()
 */
class BusinessProfile implements
    DefaultEntityInterface,
    CopyableEntityInterface,
    OxaPersonalTranslatableInterface,
    GeolocationInterface,
    PostponeRemoveInterface,
    ReportInterface,
    PostponeExportInterface,
    ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use LocationTrait;
    use SeoTrait;
    use PostponeRemoveTrait;
    use ChangeStateTrait;

    const SERVICE_AREAS_AREA_CHOICE_VALUE = 'area';
    const SERVICE_AREAS_LOCALITY_CHOICE_VALUE = 'locality';

    const ACTION_URL_TYPE_ORDER = 'order';
    const ACTION_URL_TYPE_BOOK  = 'book';

    const BUSINESS_PROFILE_FIELD_NAME_LENGTH          = 255;
    const BUSINESS_PROFILE_FIELD_DESCRIPTION_LENGTH   = 10000;
    const BUSINESS_PROFILE_FIELD_PRODUCT_LENGTH       = 10000;
    const BUSINESS_PROFILE_FIELD_BRANDS_LENGTH        = 1024;
    const BUSINESS_PROFILE_FIELD_SLOGAN_LENGTH        = 255;

    const BUSINESS_STATUS_ACTIVE   = 'active';
    const BUSINESS_STATUS_INACTIVE = 'inactive';

    const BUSINESS_PROFILE_ZIP_MAX_LENGTH = 10;
    const BUSINESS_PROFILE_URL_MAX_LENGTH = 1000;
    const BUSINESS_PROFILE_FREE_MAX_CATEGORIES_COUNT = 3;

    const DEFAULT_LOCALE = 'en';

    const TRANSLATION_LANG_EN = 'En';
    const TRANSLATION_LANG_ES = 'Es';

    const ELASTIC_INDEX = 'business_profile';
    const ELASTIC_INDEX_AD = 'business_profile_ad';
    const FLAG_IS_UPDATED = 'isUpdated';

    const ELASTIC_LOCALITIES_FILED = 'locality_ids';
    const ELASTIC_CATEGORIES_FILED = 'categories_ids';

    const DEFAULT_MILES_FROM_MY_BUSINESS = 0;
    const DISTANCE_TO_BUSINESS_PRECISION = 1;

    // translatable fields
    const BUSINESS_PROFILE_FIELD_DESCRIPTION    = 'description';
    const BUSINESS_PROFILE_FIELD_DESCRIPTION_EN = 'descriptionEn';
    const BUSINESS_PROFILE_FIELD_DESCRIPTION_ES = 'descriptionEs';
    const BUSINESS_PROFILE_FIELD_PRODUCT        = 'product';
    const BUSINESS_PROFILE_FIELD_BRANDS         = 'brands';
    const BUSINESS_PROFILE_FIELD_SLOGAN         = 'slogan';
    const BUSINESS_PROFILE_FIELD_DISCOUNT       = 'discount';

    // common fields
    const BUSINESS_PROFILE_FIELD_NAME        = 'name';
    const BUSINESS_PROFILE_FIELD_NAME_EN     = 'nameEn';
    const BUSINESS_PROFILE_FIELD_NAME_ES     = 'nameEs';
    const BUSINESS_PROFILE_FIELD_PANORAMA_ID = 'panoramaId';
    const BUSINESS_PROFILE_FIELD_EMAIL       = 'email';
    const BUSINESS_PROFILE_FIELD_DC_ORDER_ID = 'dcOrderId';
    const BUSINESS_PROFILE_FIELD_ENABLE_NOT_UNIQUE_PHONE = 'enableNotUniquePhone';

    const BUSINESS_PROFILE_FIELD_SERVICE_AREAS_TYPE     = 'serviceAreasType';
    const BUSINESS_PROFILE_FIELD_MILES_OF_MY_BUSINESS   = 'milesOfMyBusiness';

    const BUSINESS_PROFILE_FIELD_STREET_ADDRESS     = 'streetAddress';
    const BUSINESS_PROFILE_FIELD_STREET_NUMBER      = 'streetNumber';
    const BUSINESS_PROFILE_FIELD_EXTENDED_ADDRESS   = 'extendedAddress';
    const BUSINESS_PROFILE_FIELD_CROSS_STREET       = 'crossStreet';
    const BUSINESS_PROFILE_FIELD_STATE              = 'state';
    const BUSINESS_PROFILE_FIELD_CITY               = 'city';
    const BUSINESS_PROFILE_FIELD_ZIP_CODE           = 'zipCode';
    const BUSINESS_PROFILE_FIELD_CUSTOM_ADDRESS     = 'customAddress';
    const BUSINESS_PROFILE_FIELD_HIDE_ADDRESS       = 'hideAddress';
    const BUSINESS_PROFILE_FIELD_HIDE_MAP           = 'hideMap';

    const BUSINESS_PROFILE_FIELD_WEBSITE_TYPE = 'websiteItem';
    const BUSINESS_PROFILE_FIELD_ACTION_URL_TYPE  = 'actionUrlItem';
    const BUSINESS_PROFILE_FIELD_ACTION_URL_TYPE_TYPE  = 'actionUrlType';
    const BUSINESS_PROFILE_FIELD_TWITTER_URL_TYPE = 'twitterURLItem';
    const BUSINESS_PROFILE_FIELD_FACEBOOK_URL_TYPE   = 'facebookURLItem';
    const BUSINESS_PROFILE_FIELD_GOOGLE_URL_TYPE     = 'googleURLItem';
    const BUSINESS_PROFILE_FIELD_YOUTUBE_URL_TYPE    = 'youtubeURLItem';
    const BUSINESS_PROFILE_FIELD_INSTAGRAM_URL_TYPE  = 'instagramURLItem';
    const BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL_TYPE = 'tripAdvisorURLItem';
    const BUSINESS_PROFILE_FIELD_TRIP_LINKEDIN_URL_TYPE = 'linkedInURLItem';

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

    const KEYWORD_DELIMITER = ',';

    const USER_STATUS_PENDING     = 'Pending';
    const USER_STATUS_ACCEPTED    = 'Accepted';
    const USER_STATUS_REJECTED    = 'Rejected';
    const USER_STATUS_DEACTIVATED = 'Deactivated';

    // fields should not contain any of !@$%^*()+={}[]<>? characters
    const ADDRESS_FIELDS_REGEX_PATTERN = '/^[^!@$%^*()+={}\[\]<>?]*$/';
    const PHONE_NUMBER_LIKE_REGEX_PATTERN = '/[\d]{2,}[\).\- ,|]+[\d]{2,}[\-. ,|]+[\d]{2,}/';

    const BUSINESS_RATING_YELP = 'rating_yelp';
    const BUSINESS_RATING_GOOGLE = 'rating_google';
    const BUSINESS_RATING_TRIP_ADVISOR = 'rating_trip_advisor';
    const BUSINESS_RATING_FACEBOOK = 'rating_facebook';

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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @Assert\NotBlank()
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
     * @Assert\Regex(
     *     match=false,
     *     pattern=BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
     *     message="business_profile.phone_number_check_failed"
     * )
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
     * @var Testimonial[] - Business testimonials
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Testimonial",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     */
    protected $testimonials;

    /**
     * Related to BUSINESS_PROFILE_URL_MAX_LENGTH
     * @var Url - Website
     *
     * @ORM\Column(name="websiteItem", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $websiteItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="action_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $actionUrlItem;

    /**
     * @var string
     *
     * @ORM\Column(name="action_url_type", type="string", length=10, options={"default": BusinessProfile::ACTION_URL_TYPE_ORDER})
     * @Assert\Choice(callback = {"Domain\BusinessBundle\Entity\BusinessProfile", "getActionUrlTypesAssert"}, multiple = false)
     */
    protected $actionUrlType;

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
     * Field related to class constant BUSINESS_PROFILE_FIELD_BRANDS
     * Field related to class constant BUSINESS_PROFILE_FIELD_BRANDS_LENGTH
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
     * @Assert\Valid
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     * )
     */
    protected $translations;

    /**
     * @var string
     *
     * @ORM\Column(name="street_address", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern=BusinessProfile::ADDRESS_FIELDS_REGEX_PATTERN)
     * @Assert\Regex(
     *     match=false,
     *     pattern=BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
     *     message="business_profile.phone_number_check_failed"
     * )
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
     * @ORM\Column(name="state", type="string", length=30, nullable=true)
     * @Assert\Length(max=30, maxMessage="business_profile.max_length")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern=BusinessProfile::ADDRESS_FIELDS_REGEX_PATTERN)
     * @Assert\Regex(
     *     match=false,
     *     pattern=BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
     *     message="business_profile.phone_number_check_failed"
     * )
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $city;

    /**
     * Related to const BUSINESS_PROFILE_ZIP_MAX_LENGTH
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[\d\-]*$/")
     * @Assert\Length(max=10, maxMessage="business_profile.max_length")
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_address", type="string", length=255, nullable=true)
     * @Assert\Regex(pattern=BusinessProfile::ADDRESS_FIELDS_REGEX_PATTERN)
     * @Assert\Regex(
     *     match=false,
     *     pattern=BusinessProfile::PHONE_NUMBER_LIKE_REGEX_PATTERN,
     *     message="business_profile.phone_number_check_failed"
     * )
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    protected $customAddress;

    /**
     * @var string - If checkbox is checked, both address of Business and mark on map are not shown to Consumer.
     *
     * @ORM\Column(name="hide_address", type="boolean", options={"default" : 0})
     */
    protected $hideAddress = false;

    /**
     * @var bool - If checkbox is checked, google map is hidden.
     *
     * @ORM\Column(name="hide_map", type="boolean", options={"default" : 0})
     */
    protected $hideMap = false;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="linkedin_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $linkedInURLItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="twitter_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $twitterURLItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="facebook_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $facebookURLItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="google_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $googleURLItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="youtube_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $youtubeURLItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="instagram_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $instagramURLItem;

    /**
     * @var Url|null
     *
     * @ORM\Column(name="trip_advisor_url_item", type="urlType", length=1000, nullable=true)
     * @Assert\Valid()
     */
    protected $tripAdvisorURLItem;

    /**
     * Field related to const BUSINESS_PROFILE_FIELD_COUNTRY
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
     * @ORM\Column(name="service_areas_type", type="string", options={"default": "area"})
     * @Assert\Choice(choices = {"area","locality"}, multiple = false, message = "business_profile.service_areas_type")
     */
    protected $serviceAreasType;

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
     * @Assert\Valid
     * @ORM\OrderBy({"priority" = "ASC", "id" = "ASC"})
     */
    protected $phones;

    /**
     * @var BusinessProfileKeyword[] - Business Profile Keywords
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileKeyword",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @Assert\Count(max="5", maxMessage = "business_profile.keywords.max_count")
     */
    protected $keywords;

    /**
     * @var BusinessProfileAlias[] - Business Profile Aliases
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileAlias",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     */
    protected $aliases;

    /**
     * @var BusinessProfileMediaUrl[] - Business Profile Media URLs
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileMediaUrl",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $mediaUrls;

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
     * @ORM\Column(name="impressions", type="integer", nullable=false, options={"default" = 0})
     */
    protected $impressions = 0;

    /**
     * @ORM\Column(name="directions", type="integer", nullable=false, options={"default" = 0})
     */
    protected $directions = 0;

    /**
     * @ORM\Column(name="calls_mobile", type="integer", nullable=false, options={"default" = 0})
     */
    protected $callsMobile = 0;

    /**
     * @ORM\Column(name="calls_desktop", type="integer", nullable=false, options={"default" = 0})
     */
    protected $callsDesktop = 0;

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

    /** @var bool
     *
     * Store business search status, not a part of DB table. calculated during the search
     */
    protected $isAd;

    /** @var int
     *
     * Current business position at page
     */
    protected $displayedPosition;

    /**
     * Related to WORKING_HOURS_ASSOCIATED_FIELD
     * @var BusinessProfileWorkingHour[] - Business Profile working hours
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileWorkingHour",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     * @Assert\Valid
     */
    protected $collectionWorkingHours;

    /**
     * @var string
     *
     * @ORM\Column(name="working_hours_json", type="text", nullable=true)
     */
    protected $workingHoursJson;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_images", type="boolean", options={"default" : 0})
     */
    protected $hasImages;

    /**
     * @var BusinessProfileExtraSearch[] - Business Profile extra searches
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileExtraSearch",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @Assert\Count(max="5", maxMessage = "business_profile.extra_search.max_count")
     */
    protected $extraSearches;

    /**
     * @var BusinessCustomFieldCheckboxCollection[] - BusinessCustomFieldCheckboxCollection
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckboxCollection",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $checkboxCollection;

    /**
     * @var BusinessCustomFieldTextAreaCollection[] - BusinessCustomFieldTextAreaCollection
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldTextAreaCollection",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $textAreaCollection;

    /**
     * @var BusinessCustomFieldRadioButtonCollection[] - BusinessCustomFieldRadioButtonCollection
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonCollection",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $radioButtonCollection;

    /**
     * @var BusinessCustomFieldListCollection[] - BusinessCustomFieldListCollection
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListCollection",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $listCollection;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_draft", type="boolean", options={"default" : 0})
     */
    protected $isDraft = false;

    /**
     * @var $csvImportFile CSVImportFile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CSVImportFile")
     * @ORM\JoinColumn(name="import_file_id", referencedColumnName="id", nullable=true)
     */
    protected $csvImportFile;

    /**
     * @var bool
     *
     * @ORM\Column(name="enable_not_unique_phone", type="boolean", options={"default" : 0}, nullable=true)
     */
    protected $enableNotUniquePhone;

    /* @var string */
    private $statusForUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url()
     */
    protected $yelpURL;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0}, nullable=true)
     */
    protected $isShowYelpRating;

    /** @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex(pattern="/^[\S]*$/", message="business_profile.no_spaces")
     */
    protected $googlePlaceId;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0}, nullable=true)
     */
    protected $isShowGooglePlaceRating;

    /** @var string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Url()
     * @Assert\Regex("/^.+-d\d+-.+$/")
     */
    protected $tripAdvisorUrl;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0}, nullable=true)
     */
    protected $isShowTripAdvisorRating;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    protected $facebookRating;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0}, nullable=true)
     */
    protected $isShowFacebookRating;

    /**
     * @return bool
     */
    public function isEnableNotUniquePhone()
    {
        return $this->enableNotUniquePhone;
    }

    /**
     * @param bool $enableNotUniquePhone
     * @return BusinessProfile
     */
    public function setEnableNotUniquePhone($enableNotUniquePhone)
    {
        $this->enableNotUniquePhone = $enableNotUniquePhone;
        return $this;
    }

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

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param int $impressions
     * @return BusinessProfile
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * @param int $directions
     * @return BusinessProfile
     */
    public function setDirections($directions)
    {
        $this->directions = $directions;

        return $this;
    }

    /**
     * @param int $callsMobile
     * @return BusinessProfile
     */
    public function setCallsMobile($callsMobile)
    {
        $this->callsMobile = $callsMobile;

        return $this;
    }

    /**
     * @return int
     */
    public function getCallsDesktop()
    {
        return $this->callsDesktop;
    }

    /**
     * @param int $callsDesktop
     * @return BusinessProfile
     */
    public function setCallsDesktop($callsDesktop)
    {
        $this->callsDesktop = $callsDesktop;

        return $this;
    }

    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return int
     */
    public function getDirections()
    {
        return $this->directions;
    }

    /**
     * @return int
     */
    public function getCallsMobile()
    {
        return $this->callsMobile;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    /**
     * @return string
     */
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
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @ORM\Column(name="panorama_id", type="string", nullable=true, length=255)
     */
    protected $panoramaId;

    /**
     * @OneToMany(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile", mappedBy="businessToRedirect")
     */
    protected $redirectedBusinesses;

    /**
     * @var BusinessProfile $businessToRedirect
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile", inversedBy="redirectedBusinesses")
     * @ORM\JoinColumn(name="business_to_redirect", referencedColumnName="id", nullable=true)
     */
    protected $businessToRedirect;

    /**
     * @var string - keyword
     *
     * @ORM\Column(name="keyword_text", type="text", nullable=true)
     */
    private $keywordText;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->coupons                  = new ArrayCollection();
        $this->subscriptions            = new ArrayCollection();
        $this->categories               = new ArrayCollection();
        $this->areas                    = new ArrayCollection();
        $this->localities               = new ArrayCollection();
        $this->neighborhoods            = new ArrayCollection();
        $this->tags                     = new ArrayCollection();
        $this->paymentMethods           = new ArrayCollection();
        $this->businessReviews          = new ArrayCollection();
        $this->images                   = new ArrayCollection();
        $this->translations             = new ArrayCollection();
        $this->phones                   = new ArrayCollection();
        $this->collectionWorkingHours   = new ArrayCollection();
        $this->extraSearches            = new ArrayCollection();
        $this->checkboxCollection       = new ArrayCollection();
        $this->radioButtonCollection    = new ArrayCollection();
        $this->listCollection           = new ArrayCollection();
        $this->textAreaCollection       = new ArrayCollection();
        $this->keywords                 = new ArrayCollection();
        $this->aliases                  = new ArrayCollection();
        $this->tasks                    = new ArrayCollection();
        $this->mediaUrls                = new ArrayCollection();
        $this->redirectedBusinesses     = new ArrayCollection();
        $this->testimonials             = new ArrayCollection();

        $this->websiteItem        = new Url();
        $this->actionUrlItem      = new Url();
        $this->facebookURLItem    = new Url();
        $this->googleURLItem      = new Url();
        $this->instagramURLItem   = new Url();
        $this->linkedInURLItem    = new Url();
        $this->tripAdvisorURLItem = new Url();
        $this->twitterURLItem     = new Url();
        $this->youtubeURLItem     = new Url();

        $this->isClosed  = false;
        $this->isUpdated = true;
        $this->hasImages = false;
        $this->milesOfMyBusiness = self::DEFAULT_MILES_FROM_MY_BUSINESS;
        $this->serviceAreasType  = self::SERVICE_AREAS_LOCALITY_CHOICE_VALUE;
        $this->actionUrlType     = self::ACTION_URL_TYPE_ORDER;

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
     * Set websiteItem
     *
     * @param Url|null $websiteItem
     *
     * @return BusinessProfile
     */
    public function setWebsiteItem($websiteItem)
    {
        $this->websiteItem = $websiteItem;

        return $this;
    }

    /**
     * Get websiteItem
     *
     * @return Url
     */
    public function getWebsiteItem()
    {
        return $this->websiteItem;
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getWebsiteLink()
    {
        return $this->getUrlLink($this->getWebsiteItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getActionLink()
    {
        return $this->getUrlLink($this->getActionUrlItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getLinkedInLink()
    {
        return $this->getUrlLink($this->getLinkedInURLItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getTwitterLink()
    {
        return $this->getUrlLink($this->getTwitterURLItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getFacebookLink()
    {
        return $this->getUrlLink($this->getFacebookURLItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getGoogleLink()
    {
        return $this->getUrlLink($this->getGoogleURLItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getYoutubeLink()
    {
        return $this->getUrlLink($this->getYoutubeURLItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getInstagramLink()
    {
        return $this->getUrlLink($this->getInstagramURLItem());
    }

    /**
     * Get website final link
     *
     * @return string
     */
    public function getTripAdvisorLink()
    {
        return $this->getUrlLink($this->getTripAdvisorURLItem());
    }

    /**
     * @param Url $url
     *
     * @return string
     */
    public function getUrlLink($url)
    {
        $http = 'http';

        if ($url) {
            $link = $url->getUrl();
        } else {
            $link = '';
        }

        if (!$link || preg_match('/^' . $http . '/', $link)) {
            return $link;
        }

        return $http . '://' . $link;
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
     * @param BusinessProfile $businessProfile
     *
     * @return $this
     */
    public function addRedirectedBusinesses(BusinessProfile $businessProfile)
    {
        $this->redirectedBusinesses->add($businessProfile);

        return $this;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function removeRedirectedBusinesses(BusinessProfile $businessProfile)
    {
        $this->redirectedBusinesses->removeElement($businessProfile);
    }

    /**
     * @return ArrayCollection
     */
    public function getRedirectedBusinesses()
    {
        return $this->redirectedBusinesses;
    }

    public function getBusinessToRedirect()
    {
        return $this->businessToRedirect;
    }

    public function setBusinessToRedirect($businessProfile)
    {
        $this->businessToRedirect = $businessProfile;

        return $this;
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
     * @return string
     */
    public function getStatusForUser()
    {
        if ($this->statusForUser === null) {
            $statuses = [
                TaskStatusType::TASK_STATUS_OPEN     => self::USER_STATUS_PENDING,
                TaskStatusType::TASK_STATUS_CLOSED   => self::USER_STATUS_ACCEPTED,
                TaskStatusType::TASK_STATUS_REJECTED => self::USER_STATUS_REJECTED,
            ];

            $criteria = Criteria::create()
                ->where(Criteria::expr()->neq('type', TaskType::TASK_PROFILE_CLAIM))
                ->setMaxResults(1)
                ->orderBy(['id' => Criteria::DESC]);

            $tasks = $this->getTasks()->matching($criteria);
            /* @var Task|null $task */
            $task = $tasks[0] ?? null;

            if ($this->getIsActive()) {
                if ($task) {
                    $this->statusForUser = $statuses[$task->getStatus()];
                } else {
                    // business has been added by admin
                    $this->statusForUser = self::USER_STATUS_ACCEPTED;
                }
            } else {
                if ($task
                    && $task->getType() === TaskType::TASK_PROFILE_CREATE
                    && $task->getStatus() === TaskStatusType::TASK_STATUS_OPEN
                ) {
                    $this->statusForUser = self::USER_STATUS_PENDING;
                } else {
                    $this->statusForUser = self::USER_STATUS_DEACTIVATED;
                }
            }
        }

        return $this->statusForUser;
    }

    /**
     * @return bool
     */
    public function getIsEditableByUser()
    {
        return in_array(
            $this->getStatusForUser(),
            [
                self::USER_STATUS_ACCEPTED,
                self::USER_STATUS_REJECTED,
            ]
        );
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

    public function getTranslationClass(): string
    {
        return BusinessProfileTranslation::class;
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
        $this->zipCode = ZipFormatterUtil::getFormattedZip($zipCode);

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
     * @return bool
     */
    public function getHideMap()
    {
        return $this->hideMap;
    }

    /**
     * @param bool $hideMap
     *
     * @return BusinessProfile
     */
    public function setHideMap($hideMap)
    {
        $this->hideMap = $hideMap;

        return $this;
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
     * @return Url|null
     */
    public function getLinkedInURLItem()
    {
        return $this->linkedInURLItem;
    }

    /**
     * @param Url|null $linkedInURLItem
     * @return BusinessProfile
     */
    public function setLinkedInURLItem($linkedInURLItem)
    {
        $this->linkedInURLItem = $linkedInURLItem;
        return $this;
    }

    /**
     * @return Url|null
     */
    public function getTwitterURLItem()
    {
        return $this->twitterURLItem;
    }

    /**
     * @param Url|null $twitterURLItem
     * @return BusinessProfile
     */
    public function setTwitterURLItem($twitterURLItem)
    {
        $this->twitterURLItem = $twitterURLItem;

        return $this;
    }

    /**
     * @return Url|null
     */
    public function getFacebookURLItem()
    {
        return $this->facebookURLItem;
    }
    /**
     * @param Url|null $facebookURLItem
     * @return BusinessProfile
     */
    public function setFacebookURLItem($facebookURLItem)
    {
        $this->facebookURLItem = $facebookURLItem;
        return $this;
    }

    /**
     * @return Url|null
     */
    public function getGoogleURLItem()
    {
        return $this->googleURLItem;
    }

    /**
     * @param Url|null $googleURLItem
     * @return BusinessProfile
     */
    public function setGoogleURLItem($googleURLItem)
    {
        $this->googleURLItem = $googleURLItem;
        return $this;
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
     * @return Url|null
     */
    public function getYoutubeURLItem()
    {
        return $this->youtubeURLItem;
    }

    /**
     * @param Url|null $youtubeURLItem
     * @return BusinessProfile
     */
    public function setYoutubeURLItem($youtubeURLItem)
    {
        $this->youtubeURLItem = $youtubeURLItem;
        return $this;
    }

    /**
     * @return Url|null
     */
    public function getInstagramURLItem()
    {
        return $this->instagramURLItem;
    }

    /**
     * @param Url|null $instagramURLItem
     * @return BusinessProfile
     */
    public function setInstagramURLItem($instagramURLItem)
    {
        $this->instagramURLItem = $instagramURLItem;
        return $this;
    }

    /**
     * @return Url|null
     */
    public function getTripAdvisorURLItem()
    {
        return $this->tripAdvisorURLItem;
    }

    /**
     * @param Url|null $tripAdvisorURLItem
     * @return BusinessProfile
     */
    public function setTripAdvisorURLItem($tripAdvisorURLItem)
    {
        $this->tripAdvisorURLItem = $tripAdvisorURLItem;
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
     * @return ArrayCollection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    public function getPhonesJSON()
    {
        $phones = $this->phones->toArray();

        $phones = array_map(function ($phone) {
            return $phone->getPhone();
        }, $phones);

        return json_encode($phones);
    }

    /**
     * @return BusinessProfilePhone|null
     */
    public function getMainPhone()
    {
        $mainPhone = null;

        foreach ($this->getPhones() as $phone) {
            if ($phone->getType() == BusinessProfilePhone::PHONE_TYPE_MAIN) {
                $mainPhone = $phone;
                break;
            }
        }

        return $mainPhone;
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
        $address       = [];
        $addressResult = '';

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
        }

        return $addressResult;
    }

    /**
     * Single access point to get address
     * @return string
     */
    public function getShortAddress()
    {
        $addressResult = '';

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
     * Add keyword
     *
     * @param BusinessProfileKeyword $keyword
     *
     * @return BusinessProfile
     */
    public function addKeyword(BusinessProfileKeyword $keyword)
    {
        $this->keywords[] = $keyword;

        if ($keyword) {
            $keyword->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove keyword
     *
     * @param BusinessProfileKeyword $keyword
     */
    public function removeKeyword(BusinessProfileKeyword $keyword)
    {
        $this->keywords->removeElement($keyword);
    }

    /**
     * @return ArrayCollection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Add alias
     *
     * @param BusinessProfileAlias $alias
     *
     * @return BusinessProfile
     */
    public function addAlias(BusinessProfileAlias $alias)
    {
        $this->aliases[] = $alias;

        if ($alias) {
            $alias->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove alias
     *
     * @param BusinessProfileAlias $alias
     */
    public function removeAlias(BusinessProfileAlias $alias)
    {
        $this->aliases->removeElement($alias);
    }

    /**
     * @return ArrayCollection
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Add mediaUrl
     *
     * @param BusinessProfileMediaUrl $mediaUrl
     *
     * @return BusinessProfile
     */
    public function addMediaUrl(BusinessProfileMediaUrl $mediaUrl)
    {
        $this->mediaUrls[] = $mediaUrl;

        if ($mediaUrl) {
            $mediaUrl->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove MediaUrl
     *
     * @param BusinessProfileMediaUrl $mediaUrl
     */
    public function removeMediaUrl(BusinessProfileMediaUrl $mediaUrl)
    {
        $this->mediaUrls->removeElement($mediaUrl);
    }

    /**
     * @return ArrayCollection
     */
    public function getMediaUrls()
    {
        return $this->mediaUrls;
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
     * @return string
     */
    public function getCitySlug()
    {
        $catalogLocality = $this->getCatalogLocality();

        if ($catalogLocality) {
            return $catalogLocality->getSlug();
        } else {
            return '';
        }
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
     * @return BusinessProfile
     */
    public function setDistance(float $distance)
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAd()
    {
        return $this->isAd;
    }

    /**
     * @param bool $isAd
     *
     * @return BusinessProfile
     */
    public function setIsAd($isAd)
    {
        $this->isAd = $isAd;

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayedPosition()
    {
        return $this->displayedPosition;
    }

    /**
     * @param int $displayedPosition
     *
     * @return BusinessProfile
     */
    public function setDisplayedPosition($displayedPosition)
    {
        $this->displayedPosition = $displayedPosition;

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
            self::SERVICE_AREAS_LOCALITY_CHOICE_VALUE   => 'Locality',
        ];
    }

    /**
     * @return array
     */
    public static function getActionUrlTypesAssert()
    {
        return array_keys(self::getActionUrlTypes());
    }

    /**
     * @return array
     */
    public static function getActionUrlTypes()
    {
        return [
            self::ACTION_URL_TYPE_ORDER => 'business_profile.action_type.order',
            self::ACTION_URL_TYPE_BOOK  => 'business_profile.action_type.book',
        ];
    }

    /**
     * @return Url|null
     */
    public function getActionUrlItem()
    {
        return $this->actionUrlItem;
    }

    /**
     * @param Url|null $actionUrlItem
     * @return BusinessProfile
     */
    public function setActionUrlItem($actionUrlItem)
    {
        $this->actionUrlItem = $actionUrlItem;

        return $this;
    }

    /**
     * @param string $actionUrlType
     *
     * @return BusinessProfile
     */
    public function setActionUrlType($actionUrlType)
    {
        $this->actionUrlType = $actionUrlType;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionUrlType()
    {
        return $this->actionUrlType;
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

    /**
     * @return string
     */
    public function getPanoramaId()
    {
        return $this->panoramaId;
    }

    /**
     * @param string $panoramaId
     *
     * @return BusinessProfile
     */
    public function setPanoramaId($panoramaId)
    {
        $this->panoramaId = $panoramaId;

        return $this;
    }

    public function getActiveStatus()
    {
        return $this->getIsActive() ? self::BUSINESS_STATUS_ACTIVE : self::BUSINESS_STATUS_INACTIVE;
    }

    /**
     * @return bool
     */
    public function getHasVideo()
    {
        return (bool) $this->getVideo();
    }

    /**
     * @return bool
     */
    public function getHasMedia()
    {
        if ($this->getBackground() or ($this->getLogo()) or !$this->getImages()->isEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getExportAreas()
    {
        $areaList = [];

        $areas = $this->getAreas();

        foreach ($areas as $area) {
            $areaList[] = $area->getName();
        }

        return implode(', ', $areaList);
    }

    /**
     * @return string
     */
    public function getExportCategories()
    {
        $categoryList = [];

        $categories = $this->getCategories();

        foreach ($categories as $category) {
            $categoryList[] = $category->getName();
        }

        return implode(', ', $categoryList);
    }

    /**
     * @return string
     */
    public function getExportSocialFeedUrls()
    {
        $socialFeedList = [];
        $socialFeeds = $this->getMediaUrls();

        foreach ($socialFeeds as $socialFeed) {
            $socialFeedList[] = $socialFeed->getUrl();
        }

        return implode(', ', $socialFeedList);
    }

    /**
     * @return string
     */
    public function getExportSocialNetworkUrls()
    {
        $socialNetworkUrlList = [];
        $socialNetworkUrlList[] = $this->getInstagramLink();
        $socialNetworkUrlList[] = $this->getGoogleLink();
        $socialNetworkUrlList[] = $this->getFacebookLink();
        $socialNetworkUrlList[] = $this->getTwitterLink();
        $socialNetworkUrlList[] = $this->getYoutubeLink();
        $socialNetworkUrlList[] = $this->getLinkedInLink();
        $socialNetworkUrlList[] = $this->getTripAdvisorLink();

        return implode(', ', array_filter($socialNetworkUrlList));
    }

    /**
     * @return string
     */
    public function getExportPhones()
    {
        $phoneList = [];

        $phones = $this->getPhones();

        foreach ($phones as $phone) {
            $phoneList[] = $phone->getPhone();
        }

        return implode(', ', $phoneList);
    }

    /**
     * @return string
     */
    public function getExportSubscriptionPlan()
    {
        $currentSubscriptionPlan = $this->getSubscriptionPlan();

        if ($currentSubscriptionPlan) {
            $name = $currentSubscriptionPlan->getName();
        } else {
            $name = '';
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getExportSubscriptionStartDate()
    {
        $currentSubscription = $this->getSubscription();

        if ($currentSubscription) {
            $date = $currentSubscription->getStartDate()->format(AdminHelper::DATETIME_FORMAT);
        } else {
            $date = '';
        }

        return $date;
    }

    /**
     * @return string
     */
    public function getExportSubscriptionEndDate()
    {
        $currentSubscription = $this->getSubscription();

        if ($currentSubscription) {
            $date = $currentSubscription->getEndDate()->format(AdminHelper::DATETIME_FORMAT);
        } else {
            $date = '';
        }

        return $date;
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
     * @return ArrayCollection
     */
    public function getExtraSearches()
    {
        return $this->extraSearches;
    }

    /**
     * Add $workingHour
     *
     * @param BusinessProfileExtraSearch $extraSearch
     *
     * @return BusinessProfile
     */
    public function addExtraSearch($extraSearch)
    {
        $this->extraSearches[] = $extraSearch;

        if ($extraSearch) {
            $extraSearch->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $extraSearch
     *
     * @param BusinessProfileExtraSearch $extraSearch
     */
    public function removeExtraSearch(BusinessProfileExtraSearch $extraSearch)
    {
        $this->extraSearches->removeElement($extraSearch);
    }

    /**
     * @return ArrayCollection
     */
    public function getCheckboxCollection()
    {
        return $this->checkboxCollection;
    }

    /**
     * Add $checkboxCollection
     *
     * @param BusinessCustomFieldCheckboxCollection $checkboxCollection
     *
     * @return BusinessProfile
     */
    public function addCheckboxCollection($checkboxCollection)
    {
        $this->checkboxCollection[] = $checkboxCollection;

        if ($checkboxCollection) {
            $checkboxCollection->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $checkboxCollection
     *
     * @param BusinessCustomFieldCheckboxCollection $checkboxCollection
     */
    public function removeCheckboxCollection(BusinessCustomFieldCheckboxCollection $checkboxCollection)
    {
        $this->checkboxCollection->removeElement($checkboxCollection);
    }

    /**
     * @return ArrayCollection
     */
    public function getTextAreaCollection()
    {
        return $this->textAreaCollection;
    }

    /**
     * Add $textAreaCollection
     *
     * @param BusinessCustomFieldTextAreaCollection $textAreaCollection
     *
     * @return BusinessProfile
     */
    public function addTextAreaCollection(BusinessCustomFieldTextAreaCollection $textAreaCollection)
    {
        $this->textAreaCollection[] = $textAreaCollection;

        if ($textAreaCollection) {
            $textAreaCollection->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $textAreaCollection
     *
     * @param BusinessCustomFieldTextAreaCollection $textAreaCollection
     */
    public function removeTextAreaCollection(BusinessCustomFieldTextAreaCollection $textAreaCollection)
    {
        $this->textAreaCollection->removeElement($textAreaCollection);
    }

    /**
     * @return ArrayCollection
     */
    public function getRadioButtonCollection()
    {
        return $this->radioButtonCollection;
    }

    /**
     * Add $radioButtonCollection
     *
     * @param BusinessCustomFieldRadioButtonCollection $radioButtonCollection
     *
     * @return BusinessProfile
     */
    public function addRadioButtonCollection(BusinessCustomFieldRadioButtonCollection $radioButtonCollection)
    {
        $this->radioButtonCollection[] = $radioButtonCollection;

        if ($radioButtonCollection) {
            $radioButtonCollection->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $radioButtonCollection
     *
     * @param BusinessCustomFieldRadioButtonCollection $radioButtonCollection
     */
    public function removeRadioButtonCollection(BusinessCustomFieldRadioButtonCollection $radioButtonCollection)
    {
        $this->radioButtonCollection->removeElement($radioButtonCollection);
    }

    /**
     * @return ArrayCollection
     */
    public function getListCollection()
    {
        return $this->listCollection;
    }

    /**
     * Add $listCollection
     *
     * @param BusinessCustomFieldListCollection $listCollection
     *
     * @return BusinessProfile
     */
    public function addListCollection(BusinessCustomFieldListCollection $listCollection)
    {
        $this->listCollection[] = $listCollection;

        if ($listCollection) {
            $listCollection->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $listCollection
     *
     * @param BusinessCustomFieldListCollection $listCollection
     */
    public function removeListCollection(BusinessCustomFieldListCollection $listCollection)
    {
        $this->listCollection->removeElement($listCollection);
    }

    /**
     * @return Testimonial[]
     */
    public function getTestimonials()
    {
        return $this->testimonials;
    }

    /**
     * @param Testimonial $testimonial
     *
     * @return BusinessProfile
     */
    public function addTestimonial(Testimonial $testimonial)
    {
        $this->testimonials[] = $testimonial;

        if ($testimonial) {
            $testimonial->setBusinessProfile($this);
        }
        return $this;
    }

    /**
     * @param Testimonial $testimonial
     */
    public function removeTestimonial(Testimonial $testimonial)
    {
        $this->testimonials->removeElement($testimonial);
    }

    /**
     * get list of bilingual fields
     * @return array
     */
    public static function getTranslatableFields()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION,
            self::BUSINESS_PROFILE_FIELD_PRODUCT,
            self::BUSINESS_PROFILE_FIELD_BRANDS,
            self::BUSINESS_PROFILE_FIELD_SLOGAN,
            self::BUSINESS_PROFILE_FIELD_DISCOUNT,
        ];
    }

    /**
     * get list of boolean fields
     * @return array
     */
    public static function getCommonBooleanFields()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_HIDE_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_HIDE_MAP,
        ];
    }

    /**
     * get list of common task fields
     * @return array
     */
    public static function getTaskCommonFields()
    {
        return [
            // translatable field
            self::BUSINESS_PROFILE_FIELD_NAME,
            self::BUSINESS_PROFILE_FIELD_SLOGAN,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION_EN,
            self::BUSINESS_PROFILE_FIELD_DESCRIPTION_ES,
//            don't track field via business owner task
//            self::BUSINESS_PROFILE_FIELD_PANORAMA_ID,

            self::BUSINESS_PROFILE_FIELD_EMAIL,

            self::BUSINESS_PROFILE_FIELD_SERVICE_AREAS_TYPE,
            self::BUSINESS_PROFILE_FIELD_MILES_OF_MY_BUSINESS,

            self::BUSINESS_PROFILE_FIELD_STREET_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_STREET_NUMBER,
            self::BUSINESS_PROFILE_FIELD_EXTENDED_ADDRESS,
            self::BUSINESS_PROFILE_FIELD_CITY,
            self::BUSINESS_PROFILE_FIELD_ZIP_CODE,
            self::BUSINESS_PROFILE_FIELD_CUSTOM_ADDRESS,

            // geo
            self::BUSINESS_PROFILE_FIELD_LATITUDE,
            self::BUSINESS_PROFILE_FIELD_LONGITUDE,
        ];
    }

    /**
     * @return array
     */
    public static function getTaskManyToOneRelations()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_CATALOG_LOCALITY,
        ];
    }

    /**
     * @return array
     */
    public static function getTaskUrlRelations()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_WEBSITE_TYPE,
        ];
    }

    /**
     * @return array
     */
    public static function getCSVImportUrlRelations()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_WEBSITE_TYPE,
            self::BUSINESS_PROFILE_FIELD_ACTION_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_TWITTER_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_FACEBOOK_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_GOOGLE_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_YOUTUBE_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_INSTAGRAM_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL_TYPE,
            self::BUSINESS_PROFILE_FIELD_TRIP_LINKEDIN_URL_TYPE,
        ];
    }

    /**
     * @return array
     */
    public static function getTaskOneToManyRelations()
    {
        return [
            self::BUSINESS_PROFILE_RELATION_WORKING_HOURS,
            self::BUSINESS_PROFILE_RELATION_PHONES,
        ];
    }

    /**
     * @return array
     */
    public static function getTaskManyToManyRelations()
    {
        return [
            self::BUSINESS_PROFILE_RELATION_CATEGORIES,
            self::BUSINESS_PROFILE_RELATION_AREAS,
            self::BUSINESS_PROFILE_RELATION_LOCALITIES,
            self::BUSINESS_PROFILE_RELATION_NEIGHBORHOODS,
        ];
    }

    /**
     * @return array
     */
    public static function getTaskMediaManyToOneRelations()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getTaskMediaOneToManyRelations()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getTaskSeoBlock()
    {
        return [
            self::BUSINESS_PROFILE_FIELD_SEO_TITLE,
            self::BUSINESS_PROFILE_FIELD_SEO_DESCRIPTION,
        ];
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
        $currentSubscriptionPlan = $this->getSubscriptionPlan();

        if ($currentSubscriptionPlan) {
            $code = $currentSubscriptionPlan->getCode();
        } else {
            $code = SubscriptionPlanInterface::CODE_FREE;
        }

        return $code;
    }

    /**
     * @return array
     */
    public static function getExportFormats(): array
    {
        return [
            self::FORMAT_CSV => self::FORMAT_CSV,
        ];
    }

    public static function getNamesOfProhibitedAggregationRatings(): array
    {
        return [
            self::BUSINESS_RATING_TRIP_ADVISOR,
        ];
    }

    public function getRelatedKeywords(): string
    {
        $keywords = [];

        /** @var Category $category */
        foreach ($this->getCategories() as $i => $category) {
            $keywords[] = $category->getKeywordText();
        }

        return implode(self::KEYWORD_DELIMITER, array_filter($keywords));
    }

    /**
     * @param string $keywordText
     *
     * @return BusinessProfile
     */
    public function setKeywordText($keywordText)
    {
        $this->keywordText = $keywordText;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeywordText()
    {
        return $this->keywordText;
    }

    /**
     * @return bool
     */
    public function getIsAllowedShowSuggestion()
    {
        $categories = $this->getCategories();

        foreach ($categories as $category) {
            if ($category->getShowSuggestion()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @param bool $isDraft
     *
     * @return BusinessProfile
     */
    public function setIsDraft(bool $isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * @return CSVImportFile|null
     */
    public function getCsvImportFile()
    {
        return $this->csvImportFile;
    }

    /**
     * @param $csvImportFile
     *
     * @return BusinessProfile
     */
    public function setCsvImportFile($csvImportFile)
    {
        $this->csvImportFile = $csvImportFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getYelpURL()
    {
        return $this->yelpURL;
    }

    /**
     * @param string $yelpURL
     * @return BusinessProfile
     */
    public function setYelpURL($yelpURL)
    {
        $this->yelpURL = $yelpURL;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowYelpRating()
    {
        return $this->isShowYelpRating;
    }

    /**
     * @param bool $isShowYelpRating
     * @return BusinessProfile
     */
    public function setIsShowYelpRating($isShowYelpRating)
    {
        $this->isShowYelpRating = $isShowYelpRating;

        return $this;
    }

    /**
     * @return string
     */
    public function getGooglePlaceId()
    {
        return $this->googlePlaceId;
    }

    /**
     * @param string $googlePlaceId
     * @return BusinessProfile
     */
    public function setGooglePlaceId($googlePlaceId)
    {
        $this->googlePlaceId = $googlePlaceId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowGooglePlaceRating()
    {
        return $this->isShowGooglePlaceRating;
    }

    /**
     * @param bool $isShowGooglePlaceRating
     * @return BusinessProfile
     */
    public function setIsShowGooglePlaceRating($isShowGooglePlaceRating)
    {
        $this->isShowGooglePlaceRating = $isShowGooglePlaceRating;

        return $this;
    }

    /**
     * @return string
     */
    public function getTripAdvisorUrl()
    {
        return $this->tripAdvisorUrl;
    }

    /**
     * @param string $tripAdvisorUrl
     * @return BusinessProfile
     */
    public function setTripAdvisorUrl($tripAdvisorUrl)
    {
        $this->tripAdvisorUrl = $tripAdvisorUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowTripAdvisorRating()
    {
        return $this->isShowTripAdvisorRating;
    }

    /**
     * @param bool $isShowTripAdvisorRating
     * @return BusinessProfile
     */
    public function setIsShowTripAdvisorRating($isShowTripAdvisorRating)
    {
        $this->isShowTripAdvisorRating = $isShowTripAdvisorRating;

        return $this;
    }

    public function getTripAdvisorId()
    {
        $id = null;

        if ($this->tripAdvisorUrl) {
            $matches = [];
            preg_match('/(?<=.-d)\d+(?=-.+)/', $this->tripAdvisorUrl, $matches);
            $id = $matches[0] ?? null;
        }

        return $id;
    }

    public function getYelpId()
    {
        $yelpBusinessUrlPath = parse_url($this->yelpURL, PHP_URL_PATH);
        $pathParts = explode('/', $yelpBusinessUrlPath);

        return end($pathParts);
    }

    /**
     * @return float
     */
    public function getFacebookRating()
    {
        return $this->facebookRating;
    }

    /**
     * @param float $facebookRating
     * @return BusinessProfile
     */
    public function setFacebookRating($facebookRating)
    {
        $this->facebookRating = $facebookRating;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowFacebookRating()
    {
        return $this->isShowFacebookRating;
    }

    /**
     * @param bool $isShowFacebookRating
     * @return BusinessProfile
     */
    public function setIsShowFacebookRating($isShowFacebookRating)
    {
        $this->isShowFacebookRating = $isShowFacebookRating;

        return $this;
    }
}
