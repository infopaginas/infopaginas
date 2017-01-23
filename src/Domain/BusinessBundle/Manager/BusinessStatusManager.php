<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;

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
     * @param Category $entity
     */
    public function manageCategoryStatusPreUpdate(Category $entity, EntityManager $em)
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (!$entity->getIsUpdated() and empty($changeSet[Category::FLAG_IS_UPDATED])) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param BusinessProfile[] $entities
     * @param EntityManager $em
     */
    public function manageBusinessStatusPostUpdate($entities, EntityManager $em)
    {
        foreach ($entities as $entity) {
            if (!$entity->getIsUpdated()) {
                $entity->setIsUpdated(true);

                $em->getRepository('DomainBusinessBundle:BusinessProfile')->setUpdatedBusinessProfile($entity->getId());
            }
        }
    }
}
