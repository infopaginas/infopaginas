<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;

/**
 * Start/End date functionality for business profile relations
 * A record with active status can be only one for business relation
 *
 * Class DatetimePeriodStatusListener
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class DatetimePeriodStatusSubscriber implements EventSubscriber
{
    /**
     * @var SubscriptionStatusManager $subscriptionStatusManager
     */
    private $subscriptionStatusManager;

    /**
     * @var BusinessProfile[] $entities
     */
    private $entities;

    /**
     * @param SubscriptionStatusManager $manager
     */
    public function setSubscriptionStatusManager(SubscriptionStatusManager $manager)
    {
        $this->subscriptionStatusManager = $manager;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::postRemove,
            Events::postUpdate,
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->managerSubscriptionStatuses($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->managerSubscriptionStatuses($args);

        if ($entity instanceof Subscription) {
            $this->entities[] = $entity->getBusinessProfile();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function managerSubscriptionStatuses(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof DatetimePeriodStatusInterface) {
            $this->subscriptionStatusManager
                ->manageDatetimePeriodStatus($entity, $args->getEntityManager());
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Subscription) {
            $this->entities[] = $entity->getBusinessProfile();
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        if (!empty($this->entities)) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $args->getEntityManager();

            foreach ($this->entities as $entity) {
                $this->subscriptionStatusManager->updateBusinessProfileFreeSubscription($entity, $em);
            }

            $em->flush();
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        if (!empty($this->entities)) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $args->getEntityManager();

            foreach ($this->entities as $entity) {
                $this->subscriptionStatusManager->updateBusinessProfileFreeSubscription($entity, $em);
            }

            $em->flush();
        }
    }
}
