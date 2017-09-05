<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusinessProfileWorkingHourTypeValidator extends ConstraintValidator
{
    const ERROR_BLOCK_PATH = 'collectionWorkingHoursError';

    public $invalidMessage   = 'form.collectionWorkingHours.invalid';
    public $timeBlankMessage = 'form.collectionWorkingHours.blank';

    /**
     * @param BusinessProfile   $business
     * @param Constraint        $constraint
     */
    public function validate($business, Constraint $constraint)
    {
        $workingHours = $business->getCollectionWorkingHours();

        if (!$workingHours->isEmpty()) {
            if (!DayOfWeekModel::validateWorkingHoursTimeBlank($workingHours)) {
                $this->context->buildViolation($this->timeBlankMessage)
                    ->atPath(self::ERROR_BLOCK_PATH)
                    ->addViolation()
                ;
            }

            if (!DayOfWeekModel::validateWorkingHoursOverlap($workingHours)) {
                $this->context->buildViolation($this->invalidMessage)
                    ->atPath(self::ERROR_BLOCK_PATH)
                    ->addViolation()
                ;
            }
        }
    }
}
