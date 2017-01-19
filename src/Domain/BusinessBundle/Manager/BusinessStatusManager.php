<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;

/**
 * Class BusinessStatusManager
 * @package Domain\BusinessBundle\Manager
 */
class BusinessStatusManager
{
    /**
     * @param BusinessProfile $entity
     */
    public function manageBusinessStatusPreUpdate(BusinessProfile $entity, EntityManager $em)
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (!$entity->getIsUpdated() and empty($changeSet[BusinessProfile::FLAG_IS_UPDATED])) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param BusinessProfile[] $entities
     * @param EntityManager $em
     */
    public function manageBusinessStatusPostUpdate($entities, EntityManager $em)
    {
        $isUpdated = false;

        foreach ($entities as $entity) {
            if (!$entity->getIsUpdated()) {
                $entity->setIsUpdated(true);
                $isUpdated = true;
            }
        }

        if ($isUpdated) {
            $em->flush();
        }
    }
}
