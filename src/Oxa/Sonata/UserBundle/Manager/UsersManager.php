<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 10:44
 */

namespace Oxa\Sonata\UserBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use Domain\BusinessBundle\Repository\BusinessReviewRepository;
use FOS\UserBundle\Model\UserInterface;

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
        $userReviews = $this->getReviewsRepository()->findUserReviews($user);
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
     * @return BusinessReviewRepository
     */
    private function getReviewsRepository() : BusinessReviewRepository
    {
        return $this->entityManager->getRepository(BusinessReviewRepository::SLUG);
    }

    /**
     * @return BusinessProfileRepository
     */
    private function getBusinessProfileRepository() : BusinessProfileRepository
    {
        return $this->entityManager->getRepository(BusinessProfileRepository::SLUG);
    }
}
