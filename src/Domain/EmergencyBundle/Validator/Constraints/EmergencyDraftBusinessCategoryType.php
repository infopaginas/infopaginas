<?php

namespace Domain\EmergencyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class EmergencyDraftBusinessCategoryType extends NotBlank
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
