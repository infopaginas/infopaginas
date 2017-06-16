<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class BusinessProfileExtraSearch extends NotBlank
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
