<?php

namespace Domain\BusinessBundle\Util\Task;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\ChangeSetEntry;

/**
 * Class RelationChangeSetUtil
 * @package Domain\BusinessBundle\Util\Task
 */
class RelationChangeSetUtil
{
    /**
     * Prepare business profile relation entity
     *
     * @access public
     * @param ChangeSetEntry $change
     * @param EntityManagerInterface $entityManager
     * @return mixed
     */
    public static function getRelationEntityFromChangeSet(
        ChangeSetEntry $change,
        EntityManagerInterface $entityManager
    ) {
        $item = json_decode($change->getNewValue());
        $entity = null;

        if ($item and $item->id) {
            $entity = $entityManager->getRepository($change->getClassName())->find($item->id);
        }

        return $entity;
    }
}
