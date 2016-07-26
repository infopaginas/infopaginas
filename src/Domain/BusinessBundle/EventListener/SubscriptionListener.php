<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\Translation\SubscriptionPlanTranslation;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DeleteableEntityInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

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
