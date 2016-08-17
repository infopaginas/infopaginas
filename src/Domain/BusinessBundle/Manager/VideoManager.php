<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Repository\BusinessProfileRepository;

/**
 * Class VideoManager
 *
 * @package Domain\BusinessBundle\Manager
 */
class VideoManager
{
    const VIDEOS_HOMEPAGE_LIMIT = 2;

    private $em;

    private $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->repository = $entityManager->getRepository(BusinessProfileRepository::SLUG);
    }

    public function fetchHomepageVideos()
    {
        $videos = $this->getRepository()->getHomepageVideos(self::VIDEOS_HOMEPAGE_LIMIT);

        return $videos;
    }

    public function getActiveVideos()
    {
        $videos = $this->getRepository()->getVideos();

        return $videos;
    }

    private function getRepository()
    {
        return $this->repository;
    }
}
