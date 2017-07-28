<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;

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

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param SubscriptionStatusManager $manager
     */
    public function setSubscriptionStatusManager(SubscriptionStatusManager $manager)
    {
        $this->subscriptionStatusManager = $manager;
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();

        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof BusinessProfile) {
                $this->subscriptionStatusManager->manageBusinessSubscriptionCreate($entity, $em);
            }
        }

        $uow->computeChangeSets();
    }
}
