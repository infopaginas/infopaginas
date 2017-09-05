<?php

namespace Oxa\Sonata\UserBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use Domain\SiteBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;

/**
 * Class UsersManager
 * @package Oxa\Sonata\UserBundle\Manager
 */
class UsersManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /** @var GroupsManager */
    private $groupsManager;

    /** @var UserManagerInterface $fosUsersManager */
    private $fosUsersManager;

    /** @var Mailer $mailer */
    private $mailer;

    /** @var TokenGenerator $tokenGenerator */
    private $tokenGenerator;

    /**
     * UsersManager constructor.
     *
     * @param EntityManager $entityManager
     * @param GroupsManager $groupsManager
     * @param UserManagerInterface $userManagerInterface
     * @param Mailer $mailer
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        EntityManager $entityManager,
        GroupsManager $groupsManager,
        UserManagerInterface $userManagerInterface,
        Mailer $mailer,
        TokenGenerator $tokenGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->groupsManager = $groupsManager;
        $this->fosUsersManager = $userManagerInterface;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function getUserReviews(UserInterface $user) : array
    {
        $userReviews = $this->getBusinessProfileRepository()->findBusinessProfilesReviewedByUser($user);
        return $userReviews;
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function getUserBusinessProfiles(UserInterface $user) : array
    {
        $businessProfiles = $this->getBusinessProfileRepository()->findUserBusinessProfiles($user);
        return $businessProfiles;
    }

    /**
     * @param string $email
     * @return User | null
     */
    public function findUserByEmail(string $email)
    {
        $user = $this->getFOSUsersManager()->findUserByEmail($email);

        return $user;
    }

    /**
     * @param User $user
     * @param int $newRoleCode
     * @param int $oldRoleCode
     */
    public function changeUserRole(User $user, int $newRoleCode, int $oldRoleCode = 0)
    {
        if ($oldRoleCode) {
            if ($user->getRole()->getCode() == $oldRoleCode) {
                $user = $this->setNewUserRole($user, $newRoleCode);
            }
        } else {
            $user = $this->setNewUserRole($user, $newRoleCode);
        }
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return UserInterface
     * @throws \Exception
     */
    public function createMerchantForBusinessProfile(string $firstname, string $lastname, string $email)
    {
        $user = $this->getFOSUsersManager()->createUser();

        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setEnabled(true);

        $password = substr($this->getTokenGenerator()->generateToken(), 0, 8);
        $user->setPlainPassword($password);

        $merchantGroup = $this->groupsManager->findByCode(Group::CODE_MERCHANT);
        $user->setRole($merchantGroup);

        $this->getFOSUsersManager()->updateUser($user);

        $this->getMailer()->sendMerchantRegisteredEmailMessage($user, $password);

        return $user;
    }

    /**
     * @param User $user
     * @param int $newRoleCode
     * @return User
     * @throws \Exception
     */
    protected function setNewUserRole(User $user, int $newRoleCode) : User
    {
        $newGroup = $this->groupsManager->findByCode($newRoleCode);
        $user->setRole($newGroup);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @return BusinessProfileRepository
     */
    private function getBusinessProfileRepository() : BusinessProfileRepository
    {
        return $this->entityManager->getRepository(BusinessProfile::class);
    }

    /**
     * @return UserManagerInterface
     */
    private function getFOSUsersManager() : UserManagerInterface
    {
        return $this->fosUsersManager;
    }

    /**
     * @return Mailer
     */
    private function getMailer() : Mailer
    {
        return $this->mailer;
    }

    /**
     * @return TokenGenerator
     */
    private function getTokenGenerator() : TokenGenerator
    {
        return $this->tokenGenerator;
    }
}
