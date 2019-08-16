<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\CSVImportFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CSVImportFileTypeValidator extends ConstraintValidator
{
    const LENGTH = 0;
    public $notValidBusinessProfileCSVFile = 'business_profile.mass_import.not_valid_csv';

    /**
     * @param CSVImportFile $csvImportFile
     * @param Constraint $constraint
     */
    public function validate($csvImportFile, Constraint $constraint)
    {
        $filename = $csvImportFile->getFile();
        $isValid = false;
        $handle = fopen($filename, 'r');

        if ($handle) {
            $data = fgetcsv($handle, self::LENGTH, $csvImportFile->getDelimiter(), $csvImportFile->getEnclosure());

            if ($data !== false) {
                $fieldsMapping = json_decode($csvImportFile->getFieldsMappingJSON(), true);

                $fieldsMapping = array_filter($fieldsMapping);
                if (!array_diff_key(CSVImportFile::getBusinessProfileRequiredFields(), $fieldsMapping)) {
                    $isValid = true;
                }
            }
        }

        fclose($handle);
        if (!$isValid) {
            $this->context->buildViolation($this->notValidBusinessProfileCSVFile)
                ->atPath('file')
                ->addViolation();
        }
    }
}
