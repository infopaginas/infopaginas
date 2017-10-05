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
        $hasError = false;
        $path     = '';

        if ($business->getStatus() != EmergencyDraftBusiness::STATUS_APPROVED) {
            if (!$business->getCategory() and empty($business->getCustomCategory())) {
                $hasError = true;
                $path = EmergencyDraftBusiness::FIELD_CUSTOM_CATEGORY;
            }
        } elseif (!$business->getCategory()) {
            $hasError = true;
            $path = EmergencyDraftBusiness::FIELD_CATEGORY;
        }

        if ($hasError and $path) {
            $this->context->buildViolation($constraint->message)
                ->atPath($path)
                ->addViolation()
            ;
        }
    }
}
