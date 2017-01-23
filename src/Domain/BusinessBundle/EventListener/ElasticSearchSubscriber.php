<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\BusinessStatusManager;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

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

    /**
     * @param BusinessStatusManager $businessStatusManager
     */
    public function setBusinessStatusManager(BusinessStatusManager $businessStatusManager)
    {
        $this->businessStatusManager = $businessStatusManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postUpdate,
            Events::postPersist,
            Events::postRemove,
            SoftDeleteableListener::POST_SOFT_DELETE,
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

    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Subscription) {
            $this->handleSubscriptionUpdate($entity, $args->getEntityManager());
        }

        if ($entity instanceof Category) {
            $this->handleCategoryUpdate($entity, $args->getEntityManager());
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
}
