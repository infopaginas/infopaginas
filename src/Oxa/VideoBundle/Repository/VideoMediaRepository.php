<?php

namespace Oxa\VideoBundle\Repository;

use Doctrine\ORM\Internal\Hydration\IterableResult;

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

        $query = $this->getEntityManager()->createQuery($qb->getDQL());
        $query
            ->setParameter('youtubeSupport', true)
            ->setParameter('youtubeAction', $action)
        ;

        $iterateResult = $query->iterate();

        return $iterateResult;
    }
}
