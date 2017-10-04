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
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;

class EmergencyBusinessListener implements EventSubscriber
{
    /** @var $businessUpdated array */
    private $businessUpdated = [];

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
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof EmergencyBusinessWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }

            if ($entity instanceof EmergencyBusiness) {
                $changeSet = $uow->getEntityChangeSet($entity);
                $this->handleEmergencyBusinessValueDiff($entity, $changeSet, $em);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof EmergencyBusinessWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }

            if ($entity instanceof EmergencyBusiness) {
                $this->updateEmergencyCatalogItemLastUpdated($entity->getArea(), $entity->getCategory(), $em);
            }
        }

        $this->updateWorkingHoursJsonFields($em);
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
    }

    /**
     * @param EmergencyBusiness $business
     * @param array $diff
     * @param EntityManager $em
     */
    protected function handleEmergencyBusinessValueDiff($business, $diff, EntityManager $em)
    {
        if (!empty($diff['category'][0]) or !empty($diff['area'][0])) {
            if (!empty($diff['category'][0])) {
                $category = $diff['category'][0];
            } else {
                $category = $business->getCategory();
            }

            if (!empty($diff['area'][0])) {
                $area = $diff['area'][0];
            } else {
                $area = $business->getArea();
            }

            $this->updateEmergencyCatalogItemLastUpdated($area, $category, $em);
        }
    }

    /**
     * @param EmergencyArea     $area
     * @param EmergencyCategory $category
     * @param EntityManager $em
     */
    protected function updateEmergencyCatalogItemLastUpdated($area, $category, EntityManager $em)
    {
        $em->getRepository(EmergencyCatalogItem::class)->setContentUpdated(
            $area,
            $category
        );
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
