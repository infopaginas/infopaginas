<?php

namespace Domain\EmergencyBundle\Validator\Constraints;

use Domain\EmergencyBundle\Entity\EmergencyDraftBusiness;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmergencyDraftBusinessCategoryTypeValidator extends ConstraintValidator
{
    /**
     * @param EmergencyDraftBusiness $business
     * @param Constraint $constraint
     */
    public function validate($business, Constraint $constraint)
    {
        $errorPath = '';

        if ($business->getStatus() != EmergencyDraftBusiness::STATUS_APPROVED) {
            if (!$business->getCategory() and empty($business->getCustomCategory())) {
                $errorPath = EmergencyDraftBusiness::FIELD_CUSTOM_CATEGORY;
            }
        } elseif (!$business->getCategory()) {
            $errorPath = EmergencyDraftBusiness::FIELD_CATEGORY;
        }

        if ($errorPath) {
            $this->context->buildViolation($constraint->message)
                ->atPath($errorPath)
                ->addViolation()
            ;
        }
    }
}
