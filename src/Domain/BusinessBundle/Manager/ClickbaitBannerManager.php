<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\ClickbaitBanner;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class ClickbaitBannerManager extends Manager
{
    protected $em;
    private   $repository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em         = $entityManager;
        $this->repository = $entityManager->getRepository(ClickbaitBanner::class);
    }

    public function getClickbaitBannerByLocality($locality)
    {
        return $locality ? $this->repository->findOneBy(['locality' => $locality]) : null;
    }
}
