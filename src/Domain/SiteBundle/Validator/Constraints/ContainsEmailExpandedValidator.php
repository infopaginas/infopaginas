<?php

namespace Domain\SiteBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Oxa\Sonata\UserBundle\Entity\User;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Oxa\Sonata\UserBundle\Manager\UsersManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContainsEmailExpandedValidator extends ConstraintValidator
{
    const EMAIL_REGEX_PATTERN = '^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var UsersManager
     */
    protected $usersManager;

    /**
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     * @param EntityManager $entityManager
     * @param UsersManager $usersManager
     */
    public function __construct(
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        EntityManager $entityManager,
        UsersManager $usersManager
    ) {
        $this->translator     = $translator;
        $this->validator      = $validator;
        $this->entityManager  = $entityManager;
        $this->usersManager   = $usersManager;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $isCustomValid = $this->validator->validateValue($value, new Email());

        if (count($isCustomValid) > 0 && !preg_match('/' . self::EMAIL_REGEX_PATTERN . '/', $value)) {
            $this->context
                ->buildViolation($this->translator->trans('fos_user.email.invalid'))
                ->addViolation();
        }

        /**
         * @var SoftDeleteableFilter $softDeleteableFilter
         */
        $softDeleteableFilter = $this->entityManager
            ->getFilters()
            ->getFilter('softdeleteable');

        $softDeleteableFilter->disableForEntity(User::class);

        $user = $this->usersManager->getUserByEmail($value);

        if ($user) {
            $this->context
                ->buildViolation($this->translator->trans('fos_user.email.already_used'))
                ->addViolation();
        }
    }
}
