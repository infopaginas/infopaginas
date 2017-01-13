<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

/**
 * To set free plan subscription for businesses without subscription
 *
 * Class SubscriptionListener
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class SubscriptionListener implements EventSubscriber
{
    /**
     * @var SubscriptionStatusManager $subscriptionStatusManager
     */
    private $subscriptionStatusManager;

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            SoftDeleteableListener::POST_SOFT_DELETE,
        ];
    }

    /**
     * @param SubscriptionStatusManager $manager
     */
    public function setSubscriptionStatusManager(SubscriptionStatusManager $manager)
    {
        $this->subscriptionStatusManager = $manager;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof BusinessProfile) {
            $this->index($args);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Subscription) {
            $this->index($args);
        }
    }

    public function postSoftDelete(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Subscription) {
            $this->index($args);
        }
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof BusinessProfile) {
            $em = $args->getEntityManager();

            // workaround for callback update_at & update_by
            $em->refresh($entity);

            $subscription = $entity->getSubscription();

            if (!$subscription) {
                $this->subscriptionStatusManager->setBusinessProfileFreeSubscription($entity, $em);
                $em->flush();
            }
        }

        if ($entity instanceof Subscription) {
            $em = $args->getEntityManager();

            $businessProfile = $entity->getBusinessProfile();

            // workaround for callback update_at and update_by
            $em->refresh($businessProfile);

            $subscription = $businessProfile->getSubscription();

            if (!$subscription) {
                $this->subscriptionStatusManager->setBusinessProfileFreeSubscription($businessProfile, $em);
                $em->flush();
            }
        }
    }
}
