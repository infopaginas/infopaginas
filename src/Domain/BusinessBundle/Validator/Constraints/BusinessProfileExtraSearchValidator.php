<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\BusinessProfileExtraSearch;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusinessProfileExtraSearchValidator extends ConstraintValidator
{
    /**
     * @param BusinessProfileExtraSearch     $extraSearch
     * @param Constraint                     $constraint
     */
    public function validate($extraSearch, Constraint $constraint)
    {
        $path = '';

        if ($extraSearch->getServiceAreasType() == $extraSearch::SERVICE_AREAS_AREA_CHOICE_VALUE) {
            if (empty($extraSearch->getMilesOfMyBusiness())) {
                $path = 'milesOfMyBusiness';
            }
        } elseif ($extraSearch->getLocalities()->isEmpty()) {
            $path = 'localities';
        }

        if ($path) {
            $this->context->buildViolation($constraint->message)
                ->atPath($path)
                ->addViolation()
            ;
        }
    }
}
