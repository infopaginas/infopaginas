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
        $paths = [];

        if ($object->getServiceAreasType() == $object::SERVICE_AREAS_AREA_CHOICE_VALUE) {
            if (empty($object->getMilesOfMyBusiness())) {
                $paths[] = 'milesOfMyBusiness';
            }
        } else {
            if ($object->getAreas()->isEmpty()) {
                $paths[] = 'areas';
            }
            if ($object->getLocalities()->isEmpty()) {
                $paths[] = 'localities';
            }
        }

        if ($paths) {
            foreach ($paths as $path) {
                $this->context->buildViolation($constraint->message)
                    ->atPath($path)
                    ->addViolation()
                ;
            }
        }
    }
}
