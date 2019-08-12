<?php

namespace Domain\BusinessBundle\Entity;

use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Validator\Constraints\CSVImportFileType as CSVImportFileTypeValidator;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="csv_import_file")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileRepository")
 * @ORM\HasLifecycleCallbacks
 * @CSVImportFileTypeValidator()
 */
class CSVImportFile implements DefaultEntityInterface
{
    use DefaultEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - File name
     *
     * @ORM\Column(name="file", type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    protected $file;

    /**
     * @var string - Delimiter
     *
     * @ORM\Column(name="delimiter", type="string", length=1)
     * @Assert\Length(max=1)
     * @Assert\NotBlank()
     */
    protected $delimiter = ',';

    /**
     * @var string - Enclosure
     *
     * @ORM\Column(name="enclosure", type="string", length=1)
     * @Assert\Length(max=1)
     * @Assert\NotBlank()
     */
    protected $enclosure = '"';

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

    public function __toString()
    {
        return $this->getDescription() ?: '';
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return CSVImportFile
     */
    public function setFile(string $file)
    {
        $this->file = $file;

        return $this;
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

    public static function getBusinessProfileRequiredFields()
    {
        return [
            'name' => 'business_profile.fields.name',
        ];
    }

    public static function getBusinessProfileMappingFields()
    {
        $importFields = [
            'discount' => 'business_profile.fields.discount',
            'panorama_id' => 'business_profile.fields.panorama_id',
            'description' => 'business_profile.fields.description',
        ];

        return array_merge(CSVImportFile::getBusinessProfileRequiredFields(), $importFields);
    }
}
