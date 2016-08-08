<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
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
        $this->managerSubscriptionStatuses($args);
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
}
