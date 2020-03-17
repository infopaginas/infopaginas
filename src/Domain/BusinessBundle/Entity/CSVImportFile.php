<?php

namespace Domain\BusinessBundle\Entity;

use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\FileUploadEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\FileUploadEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Validator\Constraints\CSVImportFileType as CSVImportFileTypeValidator;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="csv_import_file")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @CSVImportFileTypeValidator()
 */
class CSVImportFile implements DefaultEntityInterface, FileUploadEntityInterface
{
    use DefaultEntityTrait;
    use FileUploadEntityTrait;

    public const FILE_MIME_TYPE = 'text/csv';
    public const FILE_EXTENSION = 'csv';

    public const DEFAULT_DELIMITER = ',';
    public const DEFAULT_ENCLOSURE = '"';

    public const BUSINESS_PROFILE_PHONE_MAIN = 'phone_main';
    public const BUSINESS_PROFILE_PHONE_SECONDARY = 'phone_secondary';
    public const BUSINESS_PROFILE_PHONE_FAX = 'phone_fax';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - Delimiter
     *
     * @ORM\Column(name="delimiter", type="string", length=1)
     * @Assert\Length(max=1)
     * @Assert\NotBlank()
     */
    protected $delimiter;

    /**
     * @var string - Enclosure
     *
     * @ORM\Column(name="enclosure", type="string", length=1)
     * @Assert\Length(max=1)
     * @Assert\NotBlank()
     */
    protected $enclosure;

    /**
     * @var string - JSON for csv file fields mapping
     *
     * @ORM\Column(name="fields_mapping_json", type="text")
     * @Assert\NotBlank()
     */
    protected $fieldsMappingJSON;

    /**
     * @var string - Upload File Description
     *
     * @ORM\Column(name="description", type="string", nullable=true, length=255)
     */
    protected $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_processed", type="boolean", options={"default" : 0})
     */
    protected $isProcessed = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="valid_entries_count", type="integer", nullable=true)
     */
    protected $validEntriesCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="invalid_entries_count", type="integer", nullable=true)
     */
    protected $invalidEntriesCount;

    /**
     * @var string - Invalid Entries Indexes
     *
     * @ORM\Column(name="invalid_entries_numbers", type="string", nullable=true, length=1000)
     */
    protected $invalidEntriesNumbers;

    public function __toString()
    {
        return 'id: ' . $this->getId() . ($this->getDescription() ? ', ' . $this->getDescription() : '');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     *
     * @return CSVImportFile
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     *
     * @return CSVImportFile
     */
    public function setEnclosure(string $enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed()
    {
        return $this->isProcessed;
    }

    /**
     * @param bool $isProcessed
     *
     * @return CSVImportFile
     */
    public function setIsProcessed(bool $isProcessed)
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldsMappingJSON()
    {
        return $this->fieldsMappingJSON;
    }

    /**
     * @param string $fieldsMappingJSON
     *
     * @return CSVImportFile
     */
    public function setFieldsMappingJSON(string $fieldsMappingJSON)
    {
        $this->fieldsMappingJSON = $fieldsMappingJSON;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return CSVImportFile
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getValidEntriesCount()
    {
        return $this->validEntriesCount;
    }

    /**
     * @param int $validEntriesCount
     *
     * @return CSVImportFile
     */
    public function setValidEntriesCount(int $validEntriesCount)
    {
        $this->validEntriesCount = $validEntriesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getInvalidEntriesCount()
    {
        return $this->invalidEntriesCount;
    }

    /**
     * @param int $invalidEntriesCount
     *
     * @return CSVImportFile
     */
    public function setInvalidEntriesCount(int $invalidEntriesCount)
    {
        $this->invalidEntriesCount = $invalidEntriesCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvalidEntriesNumbers()
    {
        return $this->invalidEntriesNumbers;
    }

    /**
     * @param string $invalidEntriesNumbers
     *
     * @return CSVImportFile
     */
    public function setInvalidEntriesNumbers(string $invalidEntriesNumbers)
    {
        $this->invalidEntriesNumbers = $invalidEntriesNumbers;

        return $this;
    }

    public static function getBusinessProfileRequiredFields()
    {
        return [
            BusinessProfile::BUSINESS_PROFILE_FIELD_NAME => 'business_profile.fields.name',
        ];
    }

    public static function getBusinessProfileMappingFields()
    {
        $importFields = [
            BusinessProfile::BUSINESS_PROFILE_FIELD_PANORAMA_ID => 'business_profile.fields.panorama_id',
            BusinessProfile::BUSINESS_PROFILE_FIELD_EMAIL => 'business_profile.fields.email',
            BusinessProfile::BUSINESS_PROFILE_FIELD_STREET_ADDRESS => 'business_profile.fields.streetAddress',
            BusinessProfile::BUSINESS_PROFILE_FIELD_STREET_NUMBER => 'business_profile.fields.streetNumber',
            BusinessProfile::BUSINESS_PROFILE_FIELD_EXTENDED_ADDRESS => 'business_profile.fields.extendedAddress',
            BusinessProfile::BUSINESS_PROFILE_FIELD_CROSS_STREET => 'business_profile.fields.crossStreet',
            BusinessProfile::BUSINESS_PROFILE_FIELD_STATE => 'business_profile.fields.state',
            BusinessProfile::BUSINESS_PROFILE_FIELD_CITY => 'business_profile.fields.city',
            BusinessProfile::BUSINESS_PROFILE_FIELD_ZIP_CODE => 'business_profile.fields.zipCode',
            BusinessProfile::BUSINESS_PROFILE_FIELD_CUSTOM_ADDRESS => 'business_profile.fields.customAddress',
            BusinessProfile::BUSINESS_PROFILE_FIELD_HIDE_ADDRESS => 'business_profile.fields.hideAddress',
            BusinessProfile::BUSINESS_PROFILE_FIELD_HIDE_MAP => 'business_profile.fields.hideMap',
            BusinessProfile::BUSINESS_PROFILE_FIELD_LATITUDE => 'business_profile.fields.latitude',
            BusinessProfile::BUSINESS_PROFILE_FIELD_LONGITUDE => 'business_profile.fields.longitude',
            BusinessProfile::BUSINESS_PROFILE_FIELD_DC_ORDER_ID => 'business_profile.fields.dcOrderId',

            BusinessProfile::BUSINESS_PROFILE_FIELD_WEBSITE_TYPE => 'business_profile.fields.website',
            BusinessProfile::BUSINESS_PROFILE_FIELD_ACTION_URL_TYPE => 'business_profile.fields.actionURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_ACTION_URL_TYPE_TYPE => 'business_profile.fields.actionURLType',
            BusinessProfile::BUSINESS_PROFILE_FIELD_TRIP_LINKEDIN_URL_TYPE => 'business_profile.fields.linkedInURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_TWITTER_URL_TYPE => 'business_profile.fields.twitterURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_FACEBOOK_URL_TYPE => 'business_profile.fields.facebookURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_GOOGLE_URL_TYPE => 'business_profile.fields.googleURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_YOUTUBE_URL_TYPE => 'business_profile.fields.youtubeURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_INSTAGRAM_URL_TYPE => 'business_profile.fields.instagramURL',
            BusinessProfile::BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL_TYPE => 'business_profile.fields.tripAdvisorURL',

            self::BUSINESS_PROFILE_PHONE_MAIN => 'business_profile.fields.phone_main',
            self::BUSINESS_PROFILE_PHONE_SECONDARY => 'business_profile.fields.phone_secondary',
            self::BUSINESS_PROFILE_PHONE_FAX => 'business_profile.fields.phone_fax',
        ];

        $requiredFields = self::getBusinessProfileRequiredFields();
        $translatableFields = self::getBusinessProfileTranslatableFields();

        return array_merge($requiredFields, $importFields, $translatableFields);
    }

    public static function getBusinessProfileTranslatableFields()
    {
        $fields = [];

        foreach (BusinessProfile::getTranslatableFields() as $field) {
            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $fieldKey = $field . LocaleHelper::getLangPostfix($locale);
                $fields[$fieldKey] = 'business_profile.fields.' . $fieldKey;
            }
        }

        return $fields;
    }

    public function getFileExtension(): string
    {
        return self::FILE_EXTENSION;
    }

    public function getFileMimeType(): string
    {
        return self::FILE_MIME_TYPE;
    }
}
