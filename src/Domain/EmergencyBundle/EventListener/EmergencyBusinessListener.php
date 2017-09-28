<?php

namespace Domain\EmergencyBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyBusinessWorkingHour;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Model\PageInterface;

class EmergencyBusinessListener implements EventSubscriber
{
    /** @var $businessUpdated array */
    private $businessUpdated = [];

    /** @var $emergencyDataUpdated bool */
    private $emergencyDataUpdated = false;

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
     * @param $args OnFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof EmergencyBusinessWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }

            if ($entity instanceof EmergencyBusiness) {
                $this->setEmergencyDataUpdated();
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof EmergencyBusinessWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }

            if ($entity instanceof EmergencyBusiness) {
                $this->setEmergencyDataUpdated();
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof EmergencyBusinessWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }

            if ($entity instanceof EmergencyBusiness) {
                $this->setEmergencyDataUpdated();
            }
        }

        $this->updateWorkingHoursJsonFields($em);
        $this->updateEmergencyDataUpdatedAt($em);
    }

    /**
     * @param $workingHour EmergencyBusinessWorkingHour
     */
    protected function prepareBusinessesForWorkingHoursUpdate($workingHour)
    {
        $business = $workingHour->getBusiness();

        if ($business and empty($this->businessUpdated[$business->getId()])) {
            $this->businessUpdated[$business->getId()] = $business;
        }

        $this->setEmergencyDataUpdated();
    }

    protected function setEmergencyDataUpdated()
    {
        $this->emergencyDataUpdated = true;
    }

    /**
     * @param $em EntityManager
     */
    protected function updateEmergencyDataUpdatedAt(EntityManager $em)
    {
        if ($this->emergencyDataUpdated) {
            $em->getRepository(Page::class)->setPageContentUpdated(new \Datetime(), PageInterface::CODE_EMERGENCY);
        }
    }

    /**
     * @param $em EntityManager
     */
    protected function updateWorkingHoursJsonFields(EntityManager $em)
    {
        $uow      = $em->getUnitOfWork();
        $metadata = $em->getClassMetadata(EmergencyBusiness::class);

        foreach ($this->businessUpdated as $business) {
            /** @var $business EmergencyBusiness */
            $workingHours = DayOfWeekModel::getBusinessProfileWorkingHoursJson($business);

            if ($workingHours != $business->getWorkingHoursJson()) {
                $business->setWorkingHoursJson($workingHours);
                $uow->recomputeSingleEntityChangeSet($metadata, $business);
            }
        }
    }
}
