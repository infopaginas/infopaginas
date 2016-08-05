<?php
namespace Domain\SiteBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsEmailExpandedValidator extends ConstraintValidator
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function validate($value, Constraint $constraint)
    {
        $isCustomValid = $this->container->get('validator')->validateValue($value, new Email());
        $symbolsAfterComa = strlen(substr($value, strripos($value, '.') + 1));

        if (!count($isCustomValid) && !($symbolsAfterComa>= 2 && filter_var($value, FILTER_VALIDATE_EMAIL))) {
            $this->context
                ->buildViolation($this->container->get('translator')->trans('fos_user.email.invalid'))
                ->addViolation();
        }
    }
}
