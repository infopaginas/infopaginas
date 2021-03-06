<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\CSVImportFile;
use Domain\BusinessBundle\Util\CategoryUtil;
use Domain\BusinessBundle\VO\Url;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;
use Oxa\ManagerArchitectureBundle\Model\Manager\FileUploadManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CSVImportFileManager extends FileUploadManager
{
    private const FIRST_DATA_ROW_NUMBER = 2;
    
    /** @var  ContainerInterface $container */
    protected $container;
    protected $validator;
    protected $bpManager;
    protected $phoneNumbersOfPaidProfiles = [];

    public function __construct(
        EntityManager $entityManager,
        ValidatorInterface $validator,
        ContainerInterface $container,
        BusinessProfileManager $bpManager
    ) {
        $this->validator = $validator;
        $this->container = $container;
        $this->bpManager = $bpManager;
        parent::__construct($entityManager);
    }

    public function processCSVImportFiles()
    {
        $unprocessedFilesIterator = $this->getRepository()->getUnprocessedCSVImportFileIterator();
        $normalizer = new GetSetMethodNormalizer();
        $this->phoneNumbersOfPaidProfiles = $this->getEntityManager()
            ->getRepository(BusinessProfilePhone::class)
            ->getPhoneNumbersOfPaidProfiles();

        /** @var CSVImportFile $csvImportFile */
        foreach ($unprocessedFilesIterator as $row) {
            $csvImportFile = $row[0];
            $this->removeRelatedProfiles($csvImportFile);

            $fieldsMapping = array_filter(json_decode($csvImportFile->getFieldsMappingJSON(), true));
            $validEntriesCount = 0;
            $invalidEntriesCount = 0;
            $notSavedEntries = [];
            $batchSize = 50;
            $i = 0;

            $data = $this->getDataFromFile($csvImportFile, $fieldsMapping);
            foreach ($data as $index => $entry) {
                $denormalizable = $this->getDenormalizableItems($entry);

                if ($this->validateData($denormalizable)) {
                    /** @var BusinessProfile $businessProfile */
                    $businessProfile = $normalizer->denormalize($denormalizable, BusinessProfile::class, 'array');
                    $businessProfile->setIsActive(false);
                    $businessProfile->setIsDraft(true);
                    $businessProfile->setCsvImportFile($csvImportFile);
                    $this->addTranslations($businessProfile, $denormalizable);

                    if (array_key_exists(CSVImportFile::BUSINESS_PROFILE_PHONE_MAIN, $entry)) {
                        $this->handlePhone(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_PHONE_MAIN],
                            BusinessProfilePhone::PHONE_TYPE_MAIN
                        );
                    }

                    if (array_key_exists(CSVImportFile::BUSINESS_PROFILE_PHONE_SECONDARY, $entry)) {
                        $this->handlePhone(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_PHONE_SECONDARY],
                            BusinessProfilePhone::PHONE_TYPE_SECONDARY
                        );
                    }

                    if (array_key_exists(CSVImportFile::BUSINESS_PROFILE_PHONE_FAX, $entry)) {
                        $this->handlePhone(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_PHONE_FAX],
                            BusinessProfilePhone::PHONE_TYPE_FAX
                        );
                    }

                    foreach (BusinessProfile::gerUrlTypeFields() as $urlField) {
                        if (array_key_exists($urlField, $entry)) {
                            $this->setUrlField(
                                $businessProfile,
                                $entry[$urlField],
                                $urlField
                            );
                        }
                    }

                    if (array_key_exists(CSVImportFile::BUSINESS_PROFILE_CATEGORIES, $entry)) {
                        $this->handleCategory(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_CATEGORIES]
                        );
                    }

                    $this->setClosestLocality(
                        $businessProfile,
                        $entry[BusinessProfile::BUSINESS_PROFILE_FIELD_LATITUDE],
                        $entry[BusinessProfile::BUSINESS_PROFILE_FIELD_LONGITUDE]
                    );

                    $this->em->persist($businessProfile);
                    $validEntriesCount++;

                    if (($i % $batchSize) === 0) {
                        $this->em->flush();
                        $this->em->clear(BusinessProfile::class);
                        $this->em->clear(BusinessProfilePhone::class);
                    }

                    $i++;
                } else {
                    $invalidEntriesCount++;
                    $notSavedEntries[] = $index + self::FIRST_DATA_ROW_NUMBER;
                }
            }
            $csvImportFile->setIsProcessed(true);
            $csvImportFile->setValidEntriesCount($validEntriesCount);
            $csvImportFile->setInvalidEntriesCount($invalidEntriesCount);
            $csvImportFile->setInvalidEntriesNumbers(implode(', ', $notSavedEntries));

            $this->em->flush();
        }
    }

    protected function setClosestLocality(BusinessProfile $bp, $lat, $lng)
    {
        $locationObject = new LocationValueObject(['searchCenterLat' => $lat, 'searchCenterLng' => $lng]);
        $searchDTO = SearchDataUtil::buildRequestDTO('', $locationObject, 1, 1);

        $locality = $this->bpManager->searchClosestLocalityInElastic($searchDTO);

        if ($locality) {
            $bp->setCatalogLocality($locality);
        }
    }

    protected function getDenormalizableItems($entry)
    {
        foreach (BusinessProfile::gerUrlTypeFields() as $urlField) {
            unset($entry[$urlField]);
        }

        return $entry;
    }

    protected function addTranslations(BusinessProfile $businessProfile, $data)
    {
        $translatableFields = BusinessProfile::getTranslatableFields();

        foreach ($translatableFields as $field) {
            LocaleHelper::handleTranslations($businessProfile, $field, $data);
        }
    }

    protected function getDataFromFile(CSVImportFile $csvImportFile, $fieldsMapping)
    {
        $data = [];
        $delimiter = $csvImportFile->getDelimiter();
        $enclosure = $csvImportFile->getEnclosure();
        $lines = file($csvImportFile->getFile(), FILE_SKIP_EMPTY_LINES);
        $headers = str_getcsv(array_shift($lines), $delimiter, $enclosure);

        foreach ($lines as $line) {
            if (
                mb_detect_encoding($line, CategoryUtil::ENCODING_UTF8, true) === false &&
                mb_detect_encoding($line, CategoryUtil::ENCODING_ISO_8859_1, true)
            ) {
                $line = mb_convert_encoding($line, CategoryUtil::ENCODING_UTF8, CategoryUtil::ENCODING_ISO_8859_1);
            }

            $row = [];
            $entry = str_getcsv($line, $delimiter, $enclosure);

            foreach ($fieldsMapping as $entityField => $fileField) {
                $index = array_search($fileField, $headers);
                $row[$entityField] = $entry[$index];
            }
            $data[] = array_filter($row);
        }

        return $data;
    }

    protected function validateData($data)
    {
        foreach (CSVImportFile::getBusinessProfileRequiredFields() as $field => $label) {
            if (empty($data[$field])) {
                return false;
            }
        }

        $metadata = $this->validator->getMetadataFor(BusinessProfile::class);
        foreach ($data as $field => $value) {
            if ($metadata->getPropertyMetadata($field)) {
                $constraints = $metadata->getPropertyMetadata($field)[0]->getConstraints();
                if (count($this->validator->validate($value, $constraints))) {
                    return false;
                }
            }
        }

        foreach (CSVImportFile::getBusinessProfilePhoneFields() as $field => $label) {
            if (!empty($data[$field]) && in_array($data[$field], $this->phoneNumbersOfPaidProfiles)) {
                return false;
            }
        }

        return true;
    }

    protected function handlePhone(BusinessProfile $businessProfile, $phones, $type): void
    {
        $splitPhones = array_map('trim', preg_split('/[,\/]/', $phones));

        foreach ($splitPhones as $i => $phone) {
            if ($i > 0 && $type === BusinessProfilePhone::PHONE_TYPE_MAIN) {
                $type = BusinessProfilePhone::PHONE_TYPE_SECONDARY;
            }

            $this->setPhone($businessProfile, $phone, $type);
        }
    }

    protected function setPhone(BusinessProfile $businessProfile, $phone, $type): void
    {
        $metadata = $this->validator->getMetadataFor(BusinessProfilePhone::class);
        if ($metadata->getPropertyMetadata('phone')) {
            $constraints = $metadata->getPropertyMetadata('phone')[0]->getConstraints();
            if (count($this->validator->validate($phone, $constraints))) {
                return;
            }
        }

        if ($phone) {
            $businessProfilePhone = new BusinessProfilePhone();
            $businessProfilePhone->setPhone($phone);
            $businessProfilePhone->setType($type);
            $businessProfilePhone->setPriority(BusinessProfilePhone::getPriorityByType($type));
            $this->em->persist($businessProfilePhone);

            $businessProfile->addPhone($businessProfilePhone);
        }
    }

    protected function setUrlField(BusinessProfile $businessProfile, $url, $field)
    {
        $metadata = $this->validator->getMetadataFor(Url::class);

        if ($metadata->getPropertyMetadata('url')) {
            $constraints = $metadata->getPropertyMetadata('url')[0]->getConstraints();

            if (count($this->validator->validate($url, $constraints))) {
                return;
            }
        }

        if ($url) {
            $value = new Url();
            $value->setUrl($url);

            $accessor = PropertyAccess::createPropertyAccessor();
            $accessor->setValue($businessProfile, $field, $value);
        }
    }

    protected function handleCategory(BusinessProfile $businessProfile, string $categories)
    {
        $categoryManager = $this->container->get('domain_business.manager.category');
        $category        = $categoryManager->getFirstFoundCategory($categories);

        if ($category) {
            $businessProfile->addCategory($category);
        }
    }

    protected function getCdnPath(): string
    {
        return sprintf(
            '%s/%s',
            $this->container->getParameter('amazon_aws_base_host'),
            $this->container->getParameter('amazon_aws_mass_import_directory')
        );
    }

    private function removeRelatedProfiles(CSVImportFile $importFile): void
    {
        $this->em
            ->getRepository(BusinessProfile::class)
            ->removeProfilesByImportFileId($importFile->getId())
        ;
    }
}
