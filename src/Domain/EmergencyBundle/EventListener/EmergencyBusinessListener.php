<?php

namespace Domain\EmergencyBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\EmergencyBundle\Entity\EmergencyBusiness;
use Domain\EmergencyBundle\Entity\EmergencyBusinessWorkingHour;
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Model\PageInterface;
use Domain\SiteBundle\Utils\Helpers\SiteHelper;

class EmergencyBusinessListener implements EventSubscriber
{
    /** @var $businessUpdated array */
    private $businessUpdated = [];

    /** @var $emergencyDataUpdated bool */
    private $emergencyDataUpdated = false;

    /** @var BusinessProfileManager $businessProfileManager */
    private $businessProfileManager;

    /**
     * @param BusinessProfileManager $businessProfileManager
     */
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
            Events::onFlush,
            Events::preUpdate,
            Events::prePersist,
            Events::preRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof EmergencyBusiness) {
            $this->manageBusinessFirstSymbol($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof EmergencyBusiness) {
            $this->manageBusinessStatusPreUpdate($entity, $args->getEntityManager());
            $this->manageBusinessFirstSymbol($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof EmergencyBusiness) {
            $this->businessProfileManager->removeItemFromElastic(EmergencyBusiness::ELASTIC_INDEX, $entity->getId());
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

                $changeSet = $uow->getEntityChangeSet($entity);
                $this->handleEmergencyBusinessValueDiff($entity, $changeSet, $em);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof EmergencyBusinessWorkingHour) {
                $this->prepareBusinessesForWorkingHoursUpdate($entity);
            }

            if ($entity instanceof EmergencyBusiness) {
                $this->setEmergencyDataUpdated();
                $this->updateEmergencyCatalogItemLastUpdated($entity->getArea(), $entity->getCategory(), $em);
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

    /**
     * @param EmergencyBusiness $business
     * @param EntityManager     $em
     */
    protected function manageBusinessStatusPreUpdate($business, $em)
    {
        $changeSet = $em->getUnitOfWork()->getEntityChangeSet($business);

        if (!$business->getIsUpdated() and empty($changeSet['isUpdated'])) {
            $business->setIsUpdated(true);
        }
    }

    /**
     * @param EmergencyBusiness $business
     */
    protected function manageBusinessFirstSymbol($business)
    {
        $business->setFirstSymbol(SiteHelper::getFirstSymbolFilter($business->getName()));
    }
}
