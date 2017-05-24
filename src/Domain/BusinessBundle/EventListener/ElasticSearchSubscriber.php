<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\BusinessStatusManager;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;

/**
 * set is updated flag for elastic search synchronization
 *
 * Class ElasticSearchSubscriber
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class ElasticSearchSubscriber implements EventSubscriber
{
    /**
     * @var BusinessStatusManager $businessStatusManager
     */
    private $businessStatusManager;

    /** @var ElasticSearchManager $elasticSearchManager */
    private $elasticSearchManager;

    /**
     * @param BusinessStatusManager $businessStatusManager
     */
    public function setBusinessStatusManager(BusinessStatusManager $businessStatusManager)
    {
        $this->businessStatusManager = $businessStatusManager;
    }

    /**
     * @param ElasticSearchManager $elasticSearchManager
     */
    public function setElasticSearchManager(ElasticSearchManager $elasticSearchManager)
    {
        $this->elasticSearchManager = $elasticSearchManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postUpdate,
            Events::postPersist,
            Events::postRemove,
            Events::preRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof BusinessProfile) {
            $this->businessStatusManager->manageBusinessStatusPreUpdate($entity, $args->getEntityManager());
        }

        if ($entity instanceof Category) {
            $this->businessStatusManager->manageCategoryStatusPreUpdate($entity, $args->getEntityManager());
        }

        if ($entity instanceof Locality) {
            $this->businessStatusManager->manageLocalityStatusPreUpdate($entity, $args->getEntityManager());
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Subscription) {
            $this->handleSubscriptionUpdate($entity, $args->getEntityManager());
        }

        if ($entity instanceof Category) {
            $this->handleCategoryUpdate($entity, $args->getEntityManager());
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Subscription) {
            $this->handleSubscriptionUpdate($entity, $args->getEntityManager());
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Subscription) {
            $this->handleSubscriptionUpdate($entity, $args->getEntityManager());
        }

        if ($entity instanceof Category) {
            $this->handleCategoryUpdate($entity, $args->getEntityManager());
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Category) {
            $this->handleCategoryPreRemove($entity);
        }

        if ($entity instanceof Locality) {
            $this->handleLocalityPreRemove($entity);
        }
    }

    public function handleCategoryUpdate(Category $category, EntityManager $em)
    {
        $businessProfiles = $category->getBusinessProfiles();

        $this->businessStatusManager->manageBusinessStatusPostUpdate($businessProfiles, $em);
    }

    public function handleSubscriptionUpdate(Subscription $subscription, EntityManager $em)
    {
        $businessProfile = $subscription->getBusinessProfile();

        $this->businessStatusManager->manageBusinessStatusPostUpdate([$businessProfile], $em);
    }

    public function handleLocalityPreRemove(Locality $locality)
    {
        $this->businessStatusManager->removeLocalityFromElastic($locality, $this->elasticSearchManager);
    }

    public function handleCategoryPreRemove(Category $category)
    {
        $this->businessStatusManager->removeCategoryFromElastic($category, $this->elasticSearchManager);
    }
}
