<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusinessProfileMediaUrlTypeValidator extends ConstraintValidator
{
    public $notUniqueMediaUrlTypeMessage = 'business_profile.media_url.type_not_unique';
    /**
     * @param BusinessProfile $business
     * @param Constraint $constraint
     */
    public function validate($business, Constraint $constraint)
    {
        $mediaUrls = $business->getMediaUrls();
        $types = [];

        foreach ($mediaUrls as $i => $mediaUrl) {
            if (in_array($mediaUrl->getType(), $types)) {
                $this->context->buildViolation($this->notUniqueMediaUrlTypeMessage)
                    ->atPath('mediaUrls')
                    ->addViolation();

                return;
            }
            $types[] = $mediaUrl->getType();
        }
    }
}
