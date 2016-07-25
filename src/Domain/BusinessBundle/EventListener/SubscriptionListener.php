<?php

namespace Domain\BusinessBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\Translation\SubscriptionPlanTranslation;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DeleteableEntityInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * To manage statuses for business profile relations
 * A record with active status can be only one for business relation
 *
 * Class SubscriptionListener
 * @package Oxa\Sonata\AdminBundle\EventListener
 */
class SubscriptionListener
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var DatetimePeriodStatusInterface $entityToSetStatusAsActive
     */
    private $entityToSetStatusAsActive;

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->em = $args->getEntityManager();
        $uow = $this->em->getUnitOfWork();

        // merge updated and new records
        $entities = $uow->getScheduledEntityUpdates() + $uow->getScheduledEntityInsertions();

        // set user to updated object
        array_map(function ($entity) {
            if ($entity instanceof BusinessProfile) {
                $this->applyValidStatus($entity);
            }
        }, $entities);
    }

    /**
     * @param BusinessProfile $entity
     */
    private function applyValidStatus(BusinessProfile $entity)
    {
        $uow = $this->em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($entity);

        if (!$entity->getSubscription()) {

            $freeSubscriptionPlan = $this->em
                ->getRepository('DomainBusinessBundle:SubscriptionPlan')
                ->findOneBy(['code' => SubscriptionPlanInterface::CODE_FREE]);

            $startDate = new \DateTime();
            $endDate = new \DateTime();
            $endDate->modify('+1 year');

            $subscription = new Subscription();
            $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);
            $subscription->setBusinessProfile($entity);
            $subscription->setSubscriptionPlan($freeSubscriptionPlan);
            $subscription->setStartDate($startDate);
            $subscription->setEndDate($endDate);
        }




        // set status as expired if it's
        if ($entity->isExpired()) {
            $uow->scheduleExtraUpdate($entity, [
                DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS => [
                    $oldStatus,
                    DatetimePeriodStatusInterface::STATUS_EXPIRED
                ]
            ]);

            $uow->propertyChanged(
                $entity,
                DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS,
                $oldStatus,
                DatetimePeriodStatusInterface::STATUS_EXPIRED
            );

            return;
        }

        // manage status only if we try to set it as Active
        // to make sure it's only one for business
        if (isset($changeSet[DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS]) &&
            $entity->getStatus() == DatetimePeriodStatusInterface::STATUS_ACTIVE
        ) {
            if ($this->entityToSetStatusAsActive) {
                // prevent to set active status for more than one entity
                // set status as Canceled if there is a record with Active status in current update
                $uow->scheduleExtraUpdate($entity, [
                    DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS => [
                        $oldStatus,
                        DatetimePeriodStatusInterface::STATUS_CANCELED
                    ]
                ]);

                $uow->propertyChanged(
                    $entity,
                    DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS,
                    $oldStatus,
                    DatetimePeriodStatusInterface::STATUS_CANCELED
                );
            } else {
                // cancel previous active records (in database)
                $this->entityToSetStatusAsActive = $entity;

                $baseEntities = $this->em->getRepository(get_class($entity))->findBy([
                    DatetimePeriodStatusInterface::PROPERTY_NAME_BUSINESS_PROFILE => $entity->getBusinessProfile(),
                    DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS => DatetimePeriodStatusInterface::STATUS_ACTIVE
                ]);

                foreach ($baseEntities as $baseEntity) {
                    /** @var DatetimePeriodStatusInterface $baseEntity*/
                    $uow->scheduleExtraUpdate($baseEntity, [
                        DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS => [
                            $baseEntity->getStatus(),
                            DatetimePeriodStatusInterface::STATUS_CANCELED
                        ]
                    ]);

                    $uow->propertyChanged(
                        $baseEntity,
                        DatetimePeriodStatusInterface::PROPERTY_NAME_BUSINESS_PROFILE,
                        $baseEntity->getStatus(),
                        DatetimePeriodStatusInterface::STATUS_CANCELED
                    );
                }
            }
        }
    }
}
