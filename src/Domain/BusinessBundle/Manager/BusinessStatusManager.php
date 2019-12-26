<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileExtraSearch;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;

/**
 * Class BusinessStatusManager
 * @package Domain\BusinessBundle\Manager
 */
class BusinessStatusManager
{
    /**
     * @param BusinessProfile $entity
     * @param EntityManager   $em
     */
    public function manageBusinessStatusPreUpdate(BusinessProfile $entity, EntityManager $em): void
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (empty($changeSet[BusinessProfile::FLAG_IS_UPDATED]) && !$entity->getIsUpdated()) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param Category        $entity
     * @param EntityManager   $em
     */
    public function manageCategoryStatusPreUpdate(Category $entity, EntityManager $em): void
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (empty($changeSet[Category::FLAG_IS_UPDATED]) && !$entity->getIsUpdated()) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param Locality        $entity
     * @param EntityManager   $em
     */
    public function manageLocalityStatusPreUpdate(Locality $entity, EntityManager $em): void
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (empty($changeSet[Locality::FLAG_IS_UPDATED]) && !$entity->getIsUpdated()) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param BusinessProfile[] $entities
     * @param EntityManager $em
     */
    public function manageBusinessStatusPostUpdate($entities, EntityManager $em): void
    {
        foreach ($entities as $entity) {
            if (!$entity->getIsUpdated()) {
                $entity->setIsUpdated(true);

                $em->getRepository(BusinessProfile::class)->setUpdatedBusinessProfile($entity->getId());
            }
        }
    }

    /**
     * @param Category             $entity
     * @param ElasticSearchManager $elasticSearch
     */
    public function removeCategoryFromElastic(Category $entity, ElasticSearchManager $elasticSearch): void
    {
        $this->removeItemFromElastic($entity->getId(), Category::ELASTIC_INDEX, $elasticSearch);
    }

    /**
     * @param Locality             $entity
     * @param ElasticSearchManager $elasticSearch
     */
    public function removeLocalityFromElastic(Locality $entity, ElasticSearchManager $elasticSearch): void
    {
        $this->removeItemFromElastic($entity->getId(), Locality::ELASTIC_INDEX, $elasticSearch);
    }

    /**
     * @param BusinessProfileExtraSearch $entity
     * @param ElasticSearchManager       $elasticSearch
     */
    public function removeExtraSearchFromElastic($entity, $elasticSearch): void
    {
        $this->removeItemFromElastic($entity->getId(), BusinessProfile::ELASTIC_INDEX_AD, $elasticSearch);
    }

    /**
     * @param int       $id
     * @param string    $index
     * @param ElasticSearchManager $elasticSearch
     *
     * @return bool
     */
    protected function removeItemFromElastic($id, $index, ElasticSearchManager $elasticSearch): bool
    {
        $status = true;

        try {
            $elasticSearch->deleteItem($index, $id);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            if (!empty($message->error->type) &&
                $message->error->type == ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }
}
