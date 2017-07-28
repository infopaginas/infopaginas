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
    public function manageBusinessStatusPreUpdate(BusinessProfile $entity, EntityManager $em)
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (!$entity->getIsUpdated() and empty($changeSet[BusinessProfile::FLAG_IS_UPDATED])) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param Category        $entity
     * @param EntityManager   $em
     */
    public function manageCategoryStatusPreUpdate(Category $entity, EntityManager $em)
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (!$entity->getIsUpdated() and empty($changeSet[Category::FLAG_IS_UPDATED])) {
            $entity->setIsUpdated(true);
        }
    }

    /**
     * @param Locality        $entity
     * @param EntityManager   $em
     */
    public function manageLocalityStatusPreUpdate(Locality $entity, EntityManager $em)
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

        if (!$entity->getIsUpdated() and empty($changeSet[Locality::FLAG_IS_UPDATED])) {
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

                $em->getRepository(BusinessProfile::class)->setUpdatedBusinessProfile($entity->getId());
            }
        }
    }

    /**
     * @param Category             $entity
     * @param ElasticSearchManager $elasticSearch
     */
    public function removeCategoryFromElastic(Category $entity, ElasticSearchManager $elasticSearch)
    {
        $this->removeItemFromElastic($entity->getId(), Category::ELASTIC_DOCUMENT_TYPE, $elasticSearch);
    }

    /**
     * @param Locality             $entity
     * @param ElasticSearchManager $elasticSearch
     */
    public function removeLocalityFromElastic(Locality $entity, ElasticSearchManager $elasticSearch)
    {
        $this->removeItemFromElastic($entity->getId(), Locality::ELASTIC_DOCUMENT_TYPE, $elasticSearch);
    }

    /**
     * @param BusinessProfileExtraSearch $entity
     * @param ElasticSearchManager       $elasticSearch
     */
    public function removeExtraSearchFromElastic($entity, $elasticSearch)
    {
        $this->removeItemFromElastic($entity->getId(), BusinessProfile::ELASTIC_DOCUMENT_TYPE_AD, $elasticSearch);
    }

    /**
     * @param int       $id
     * @param string    $documentType
     * @param ElasticSearchManager $elasticSearch
     *
     * @return bool
     */
    protected function removeItemFromElastic($id, $documentType, ElasticSearchManager $elasticSearch)
    {
        $status = true;

        try {
            $response = $elasticSearch->deleteItem($id, $documentType);
        } catch (\Exception $e) {
            $status = false;
            $message = json_decode($e->getMessage());

            if (!empty($message->error->type) and
                $message->error->type == ElasticSearchManager::INDEX_NOT_FOUND_EXCEPTION
            ) {
                $status = true;
            }
        }

        return $status;
    }
}
