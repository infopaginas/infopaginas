<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 10:44
 */

namespace Oxa\Sonata\UserBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use FOS\UserBundle\Model\UserInterface;
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

    /**
     * UsersManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
    public function getUserByEmail(string $email)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        return $user;
    }

    /**
     * @return BusinessReviewRepository
     */
    private function getReviewsRepository() : BusinessReviewRepository
    {
        return $this->entityManager->getRepository(BusinessReview::class);
    }

    /**
     * @return BusinessProfileRepository
     */
    private function getBusinessProfileRepository() : BusinessProfileRepository
    {
        return $this->entityManager->getRepository(BusinessProfile::class);
    }
}
