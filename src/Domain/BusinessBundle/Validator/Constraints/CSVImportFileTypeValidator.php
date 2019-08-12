<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\CSVImportFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CSVImportFileTypeValidator extends ConstraintValidator
{
    const LENGTH = 1000;
    public $notValidBusinessProfileCSVFile = 'business_profile.mass_import.not_valid_csv';

    /**
     * @param CSVImportFile $csvImportFile
     * @param Constraint $constraint
     */
    public function validate($csvImportFile, Constraint $constraint)
    {
        $filename = $csvImportFile->getFile();
        $isValid = false;

        if (($handle = fopen($filename, "r")) !== false) {
            $data = fgetcsv($handle, self::LENGTH, $csvImportFile->getDelimiter(), $csvImportFile->getEnclosure());

            if ($data !== false) {
                $isValid = true;
                $fieldsMapping = json_decode($csvImportFile->getFieldsMappingJSON(), true);
                foreach (CSVImportFile::getBusinessProfileRequiredFields() as $field => $label) {
                    if (!in_array($fieldsMapping[$field], $data)) {
                        $isValid = false;
                        break;
                    }
                }
            }
            fclose($handle);
        }

        if (!$isValid) {
            $this->context->buildViolation($this->notValidBusinessProfileCSVFile)
                ->atPath('file')
                ->addViolation();
        }
    }
}
