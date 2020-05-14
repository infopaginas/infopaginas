<?php

namespace Domain\SiteBundle\Validator\Constraints;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContainsEmailExpandedValidator extends ConstraintValidator
{
    const EMAIL_REGEX_PATTERN = '^([a-zA-Z0-9_-]+\.)*[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*\.[a-zA-Z]{2,6}$';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     */
    public function __construct(TranslatorInterface $translator, ValidatorInterface $validator)
    {
        $this->translator   = $translator;
        $this->validator    = $validator;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $isCustomValid = $this->validator->validate($value, new Email());

        if ($value && count($isCustomValid) == 0 && !preg_match('/' . self::EMAIL_REGEX_PATTERN . '/', $value)) {
            $this->context
                ->buildViolation($this->translator->trans('fos_user.email.invalid'))
                ->addViolation();
        }
    }
}
