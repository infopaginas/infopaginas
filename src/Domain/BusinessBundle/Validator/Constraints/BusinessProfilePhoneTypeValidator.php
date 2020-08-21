<?php

namespace Domain\BusinessBundle\Validator\Constraints;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusinessProfilePhoneTypeValidator extends ConstraintValidator
{
    public const ERROR_BLOCK_PATH = 'phoneCollection';

    public $notUniqueMainPhoneMessage   = 'business_profile_phone.not_unique_main';
    public $noMainPhoneMessage          = 'business_profile_phone.no_main';

    /**
     * @param BusinessProfile   $business
     * @param Constraint        $constraint
     */
    public function validate($business, Constraint $constraint)
    {
        $phones = $business->getPhones();

        $hasMainPhone = false;

        if (!$business->getPhones()->isEmpty()) {
            foreach ($phones as $phone) {
                if ($phone->getType() == BusinessProfilePhone::PHONE_TYPE_MAIN) {
                    if ($hasMainPhone) {
                        $this->context->buildViolation($this->notUniqueMainPhoneMessage)
                            ->atPath(self::ERROR_BLOCK_PATH)
                            ->addViolation()
                        ;

                        break;
                    }

                    $hasMainPhone = true;
                }
            }

            if (!$hasMainPhone) {
                $this->context->buildViolation($this->noMainPhoneMessage)
                    ->atPath(self::ERROR_BLOCK_PATH)
                    ->addViolation()
                ;
            }
        }
    }
}
