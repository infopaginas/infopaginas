<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\CSVImportFile;
use Domain\BusinessBundle\VO\Url;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Gaufrette\Filesystem;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CSVImportFileManager extends Manager
{
    const FILE_TYPE = 'text/csv';

    /** @var  ContainerInterface $container */
    protected $container;

    /** @var  Filesystem $filesystem */
    protected $filesystem;

    protected $validator;

    /**
     * @param EntityManager $entityManager
     * @param Filesystem $filesystem
     * @param ValidatorInterface $validator
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManager $entityManager,
        Filesystem $filesystem,
        ValidatorInterface $validator,
        ContainerInterface $container
    ) {
        $this->filesystem = $filesystem;
        $this->validator = $validator;
        $this->container = $container;
        parent::__construct($entityManager);
    }

    public function upload(CSVImportFile $csvImportFile)
    {
        $adapter = $this->filesystem->getAdapter();
        $tempFilePath = $csvImportFile->getFile();

        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.csv', uniqid('', true));
        } while ($adapter->exists($path . $filename));

        $adapter->setMetadata($path . $filename, [
            'contentType'   => self::FILE_TYPE,
            'ACL'           => 'public-read',
        ]);

        $uploadedSize = $adapter->write($path . $filename, file_get_contents($tempFilePath));

        return [
            'status' => (bool) $uploadedSize,
            'link'   => $this->getPublicUrl($path, $filename),
        ];
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

                    foreach (BusinessProfile::getCSVImportUrlRelations() as $urlField) {
                        if (array_key_exists($urlField, $entry)) {
                            $this->setUrlField(
                                $businessProfile,
                                $entry[$urlField],
                                $urlField
                            );
                        }
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
        foreach (BusinessProfile::getCSVImportUrlRelations() as $urlField) {
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

    /**
     * @param string $path
     * @param string $filename
     *
     * @return string
     */
    protected function getPublicUrl($path, $filename)
    {
        $url = sprintf(
            '%s/%s%s',
            $this->getCdnPath(),
            $path,
            $filename
        );

        return $url;
    }

    /**
     * @return string
     */
    protected function getCdnPath()
    {
        $path = sprintf(
            '%s/%s',
            $this->container->getParameter('amazon_aws_base_host'),
            $this->container->getParameter('amazon_aws_mass_import_directory')
        );

        return $path;
    }
}
