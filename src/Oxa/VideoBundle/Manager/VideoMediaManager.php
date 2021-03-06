<?php

namespace Oxa\VideoBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Repository\VideoMediaRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VideoMediaManager
{
    private $em;

    private $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->repository = $entityManager->getRepository(VideoMedia::class);
    }

    public function find(int $id)
    {
        return $this->getRepository()->find($id);
    }

    public function save(array $videoMediaData) : VideoMedia
    {
        $videoMedia = new VideoMedia($videoMediaData);

        $this->em->persist($videoMedia);
        $this->em->flush();

        return $videoMedia;
    }

    public function updateNameAndDescriptionByVideoID(VideoMedia $media)
    {
        /** @var VideoMedia $videoMedia */
        $videoMedia = $this->getRepository()->findOneBy(['id' => $media->getId()]);

        if (!$videoMedia) {
            throw new NotFoundHttpException('Mediafile is not found');
        }

        $this->em->persist($videoMedia);

        return $videoMedia;
    }

    private function getRepository()
    {
        return $this->repository;
    }
}
