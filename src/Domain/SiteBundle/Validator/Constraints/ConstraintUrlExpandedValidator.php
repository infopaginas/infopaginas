<?php

namespace Domain\SiteBundle\Validator\Constraints;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintUrlExpandedValidator extends ConstraintValidator
{
    const URL_REGEXP_PATTERN = '^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)*\/?$';
    const TRANSLATION_DOMAIN = 'DomainSiteBundle';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value && !preg_match('/' . self::URL_REGEXP_PATTERN . '/', $value)) {
            $this->context
                ->buildViolation($this->translator->trans('business_profile.url.invalid', [], self::TRANSLATION_DOMAIN))
                ->addViolation();
        }
    }
}
