<?php

namespace Oxa\VideoBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\YoutubeManager;

/**
 * VideoMediaRepository
 *
 * An VideoMediaRepository serves as a repository for entities with generic as well as
 * business specific methods for retrieving entities.
 */
class VideoMediaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string $action
     * @return IterableResult
     */
    public function getVideoForYoutubeActionIterator($action)
    {
        $qb = $this->createQueryBuilder('v');
        $qb
            ->andWhere('v.youtubeSupport = :youtubeSupport')
            ->andWhere('v.youtubeAction = :youtubeAction')
            ->setParameter('youtubeSupport', true)
            ->setParameter('youtubeAction', $action)
        ;

        if ($action != VideoMedia::YOUTUBE_ACTION_ADD) {
            $qb->andWhere('v.youtubeId IS NOT NULL');
        }

        $query = $this->getEntityManager()->createQuery($qb->getDQL());
        $query
            ->setParameter('youtubeSupport', true)
            ->setParameter('youtubeAction', $action)
        ;

        $videoIterator = $query->iterate();

        return $videoIterator;
    }

    /**
     * @return IterableResult
     */
    public function getConvertVideos($status)
    {
        $qb = $this->createQueryBuilder('v');
        $qb
            ->andWhere('v.status = :status')
            ->setParameter('status', $status)
        ;

        $query = $this->getEntityManager()->createQuery($qb->getDQL());
        $query->setParameter('status', $status);

        $videoIterator = $query->iterate();

        return $videoIterator;
    }

    /**
     * @param bool $getAll
     *
     * @return IterableResult
     */
    public function getActiveVideoIterator($getAll = false)
    {
        $qb = $this->getActiveVideoQueryBuilder();

        if (!$getAll) {
            $qb->andWhere('v.poster IS NULL');
        }

        $query = $this->getEntityManager()->createQuery($qb->getDQL());
        $query->setParameter('status', VideoMedia::VIDEO_STATUS_ACTIVE);

        return $query->iterate();
    }

    public function getActiveVideosWithValidYoutubeIdIterator()
    {
        $qb = $this->getActiveVideoQueryBuilder();

        $qb->andWhere('v.youtubeId IS NOT NULL')
            ->andWhere('v.uploadStatus != :invalidStatus OR v.uploadStatus IS NULL')
            ->setParameter('invalidStatus', VideoMedia::INVALID_YOUTUBE_ID_STATUS)
        ;

        return $qb->getQuery()->iterate();
    }

    public function getAllYoutubeIds(): ?array
    {
        $qb = $this->createQueryBuilder('v');
        $qb
            ->select('v.youtubeId as ' . YoutubeManager::YOUTUBE_ID_COLUMN_ALIAS)
            ->andWhere('v.youtubeId IS NOT NULL')
            ->andWhere('v.uploadStatus != :invalidStatus OR v.uploadStatus IS NULL')
            ->setParameter('invalidStatus', VideoMedia::INVALID_YOUTUBE_ID_STATUS)
        ;

        return $qb->getQuery()->getResult();
    }

    private function getActiveVideoQueryBuilder()
    {
        $qb = $this->createQueryBuilder('v');
        $qb
            ->andWhere('v.status = :status')
            ->orderBy('v.id')
            ->setParameter('status', VideoMedia::VIDEO_STATUS_ACTIVE)
        ;

        return $qb;
    }
}
