<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileExtraSearch;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ServiceAreaTypeValidator extends ConstraintValidator
{
    /**
     * @param BusinessProfile|BusinessProfileExtraSearch     $object
     * @param Constraint $constraint
     */
    public function validate($object, Constraint $constraint)
    {
        $path = '';

        if ($object->getServiceAreasType() == $object::SERVICE_AREAS_AREA_CHOICE_VALUE) {
            if (empty($object->getMilesOfMyBusiness())) {
                $path = 'milesOfMyBusiness';
            }
        } elseif ($object->getLocalities()->isEmpty()) {
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
