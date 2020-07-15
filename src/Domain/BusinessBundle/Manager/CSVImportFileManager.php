<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\CSVImportFile;
use Domain\BusinessBundle\VO\Url;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ManagerArchitectureBundle\Model\Manager\FileUploadManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CSVImportFileManager extends FileUploadManager
{
    /** @var  ContainerInterface $container */
    protected $container;

    protected $validator;

    /**
     * @param EntityManager $entityManager
     * @param ValidatorInterface $validator
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManager $entityManager,
        ValidatorInterface $validator,
        ContainerInterface $container
    ) {
        $this->validator = $validator;
        $this->container = $container;
        parent::__construct($entityManager);
    }

    public function processCSVImportFiles()
    {
        $unprocessedFiles = $this->getRepository()->findBy(['isProcessed' => false]);
        $normalizer = new GetSetMethodNormalizer();

        /** @var CSVImportFile $csvImportFile */
        foreach ($unprocessedFiles as $csvImportFile) {
            $fieldsMapping = array_filter(json_decode($csvImportFile->getFieldsMappingJSON(), true));
            $validEntriesCount = 0;
            $invalidEntriesCount = 0;
            $notSavedIndexes = [];

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
                        $this->setPhone(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_PHONE_MAIN],
                            BusinessProfilePhone::PHONE_TYPE_MAIN
                        );
                    }

                    if (array_key_exists(CSVImportFile::BUSINESS_PROFILE_PHONE_SECONDARY, $entry)) {
                        $this->setPhone(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_PHONE_SECONDARY],
                            BusinessProfilePhone::PHONE_TYPE_SECONDARY
                        );
                    }

                    if (array_key_exists(CSVImportFile::BUSINESS_PROFILE_PHONE_FAX, $entry)) {
                        $this->setPhone(
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
                        $this->setCategory(
                            $businessProfile,
                            $entry[CSVImportFile::BUSINESS_PROFILE_CATEGORIES]
                        );
                    }

                    $this->em->persist($businessProfile);
                    $validEntriesCount++;
                } else {
                    $invalidEntriesCount++;
                    $notSavedIndexes[] = $index;
                }
            }
            $csvImportFile->setIsProcessed(true);
            $csvImportFile->setValidEntriesCount($validEntriesCount);
            $csvImportFile->setInvalidEntriesCount($invalidEntriesCount);
            $csvImportFile->setInvalidEntriesNumbers(implode(', ', $notSavedIndexes));

            $this->em->flush();
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
        $metadata = $this->validator->getMetadataFor(BusinessProfile::class);
        foreach ($data as $field => $value) {
            if ($metadata->getPropertyMetadata($field)) {
                $constraints = $metadata->getPropertyMetadata($field)[0]->getConstraints();
                if (count($this->validator->validate($value, $constraints))) {
                    return false;
                }
            }
        }

        foreach (CSVImportFile::getBusinessProfileRequiredFields() as $field => $label) {
            if (empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    protected function setPhone(BusinessProfile $businessProfile, $phone, $type)
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

    protected function setCategory(BusinessProfile $businessProfile, $categoryName)
    {
        $categoryManager = $this->container->get('domain_business.manager.category');
        $category = $categoryManager->getCategoryByName($categoryName);
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
}
