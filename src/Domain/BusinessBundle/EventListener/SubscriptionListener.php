<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;

/**
 * To set free plan subscription for businesses without subscription
 *
 * Class SubscriptionListener
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class SubscriptionListener
{
    /**
     * @var SubscriptionStatusManager $subscriptionStatusManager
     */
    private $subscriptionStatusManager;

    /**
     * @param SubscriptionStatusManager $manager
     */
    public function setSubscriptionStatusManager(SubscriptionStatusManager $manager)
    {
        $this->subscriptionStatusManager = $manager;
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $businessProfiles = $this->subscriptionStatusManager->setFreeSubscription($em);

        if ($businessProfiles) {
            $em->getEventManager()->removeEventListener(Events::postFlush, $this);
            $em->flush();
        }
    }
}
