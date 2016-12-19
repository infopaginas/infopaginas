<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\EventSubscriber;

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

    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $this->entities = [];
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $event->getEntityManager();
        /* @var $uow \Doctrine\ORM\UnitOfWork */
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof BusinessProfile) {
                $this->entities[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof BusinessProfile) {
                $this->entities[$entity->getId()] = $entity;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty($this->entities)) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $event->getEntityManager();

            foreach ($this->entities as $entity) {
                $this->subscriptionStatusManager->updateBusinessProfileFreeSubscription($entity, $em);
            }

            $em->flush();
        }
    }
}
