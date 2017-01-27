<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\StatusInterface;

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

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof BusinessProfile) {
            $em = $args->getEntityManager();

            $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

            if (!empty($changeSet[BusinessProfile::BUSINESS_PROFILE_FIELD_SUBSCRIPTIONS])) {
                $subscription = $entity->getSubscription();

                if (!$subscription) {
                    $this->subscriptionStatusManager->setBusinessProfileFreeSubscription($entity, $em);
                    $em->flush();
                }
            }
        }

        if ($entity instanceof Subscription) {
            $em = $args->getEntityManager();

            $businessProfile = $entity->getBusinessProfile();

            if ($businessProfile) {
                $changeSet = $em->getUnitOfWork()->getEntityChangeSet($entity);

                if (empty($changeSet[Subscription::PROPERTY_NAME_UPDATED_AT]) or count($changeSet) > 1) {
                    $subscription = $businessProfile->getSubscription();

                    if (!$subscription) {
                        $this->subscriptionStatusManager->setBusinessProfileFreeSubscription($businessProfile, $em);
                        $em->flush();
                    }
                }
            }
        }
    }
}
