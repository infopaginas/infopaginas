<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileWorkingHour;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Oxa\VideoBundle\Entity\VideoMedia;

class BusinessProfileListener implements EventSubscriber
{
    /** @var BusinessProfileManager $businessProfileManager */
    private $businessProfileManager;

    /** @var $businessUpdated array */
    private $businessUpdated = [];

    public function setBusinessProfileManager($businessProfileManager)
    {
        $this->businessProfileManager = $businessProfileManager;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::onFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof BusinessProfile) {
            $this->businessProfileManager->removeBusinessFromElastic($entity->getId());

            if ($entity->getVideo()) {
                $media = $entity->getVideo();

                $media->setIsDeleted(true);
            }
        }
    }

    /**
     * @param $args OnFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof BusinessProfileWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof BusinessProfileWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof BusinessProfileWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }
        }

        $this->updateWorkingHoursJsonFields($em);
    }

    /**
     * @param $workingHour BusinessProfileWorkingHour
     */
    protected function prepareBusinessesForWorkingHoursUpdate($workingHour)
    {
        $business = $workingHour->getBusinessProfile();

        if ($business and empty($this->businessUpdated[$business->getId()])) {
            $this->businessUpdated[$business->getId()] = $business;
        }
    }

    /**
     * @param $em EntityManager
     */
    protected function updateWorkingHoursJsonFields(EntityManager $em)
    {
        $uow      = $em->getUnitOfWork();
        $metadata = $em->getClassMetadata(BusinessProfile::class);

        foreach ($this->businessUpdated as $business) {
            /** @var $business BusinessProfile */
            $workingHours = DayOfWeekModel::getBusinessProfileWorkingHoursJson($business);

            if ($workingHours != $business->getWorkingHoursJson()) {
                $business->setWorkingHoursJson($workingHours);
                $uow->recomputeSingleEntityChangeSet($metadata, $business);
            }
        }
    }
}
