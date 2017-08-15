<?php

namespace Domain\SiteBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Oxa\Sonata\UserBundle\Entity\User;
use Oxa\Sonata\UserBundle\Manager\UsersManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintEmailUniqueValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

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
     * @param EntityManager       $entityManager
     * @param UsersManager        $usersManager
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityManager $entityManager,
        UsersManager $usersManager
    ) {
        $this->translator     = $translator;
        $this->entityManager  = $entityManager;
        $this->usersManager   = $usersManager;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value) {
            $user = $this->usersManager->findUserByEmail($value);

            if ($user) {
                $this->context
                    ->buildViolation($this->translator->trans('fos_user.email.already_used'))
                    ->addViolation();
            }
        }
    }
}
