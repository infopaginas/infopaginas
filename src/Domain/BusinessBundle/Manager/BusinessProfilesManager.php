<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 04.07.16
 * Time: 18:12
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BusinessProfilesManager
 * @package Domain\BusinessBundle\Manager
 */
class BusinessProfilesManager
{
    /** @var EntityManager */
    private $entityManager;

    /** @var EntityRepository */
    private $repository;

    /** @var UserInterface */
    private $currentUser;

    public function __construct(EntityManager $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;

        $this->repository = $entityManager->getRepository(BusinessProfileRepository::SLUG);

        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    public function find(int $id, string $locale = 'en_US')
    {
        $business = $this->getRepository()->find($id);

        if ($locale !== 'en_US') {
            $business->setLocale($locale);
            $this->getEntityManager()->refresh($business);
        }

        return $business;
    }

    public function createProfile() : BusinessProfile
    {
        return new BusinessProfile();
    }

    public function saveProfile(BusinessProfile $businessProfile, string $locale = 'en_EN')
    {
        $businessProfile->setLocale($locale);
        $businessProfile->setUser($this->currentUser);

        $this->getEntityManager()->persist($businessProfile);
        $this->getEntityManager()->flush();
    }

    private function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }

    private function getRepository() : EntityRepository
    {
        return $this->repository;
    }
}