<?php
namespace Domain\SiteBundle\Validator\Constraints;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContainsEmailExpandedValidator extends ConstraintValidator
{
    const EMAIL_REGEX_PATTERN = '(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})';

    protected $translator;
    protected $validator;

    public function __construct(TranslatorInterface $translator, ValidatorInterface $validator)
    {
        $this->translator = $translator;
        $this->validator  = $validator;
    }

    public function validate($value, Constraint $constraint)
    {
        $isCustomValid = $this->validator->validateValue($value, new Email());

        if (!count($isCustomValid) && !preg_match('/' . self::EMAIL_REGEX_PATTERN . '/', $value)) {
            $this->context
                ->buildViolation($this->translator->trans('fos_user.email.invalid'))
                ->addViolation();
        }
    }
}
