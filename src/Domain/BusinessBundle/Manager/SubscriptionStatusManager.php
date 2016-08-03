<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/14/16
 * Time: 12:02 PM
 */

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;

/**
 * Class SubscriptionStatusManager
 * @package Domain\BusinessBundle\Manager
 */
class SubscriptionStatusManager
{
    /**
     * @var DatetimePeriodStatusInterface|null
     */
    private $entityToSetStatusAsActive;

    /**
     * @param DatetimePeriodStatusInterface $entity
     * @param EntityManager $em
     */
    public function manageDatetimePeriodStatus(DatetimePeriodStatusInterface $entity, EntityManager $em)
    {
        $changeSet = $em->getUnitOfWork()
            ->getEntityChangeSet($entity);

        // set status as expired if it's
        if ($entity->isExpired()) {
            $entity->setStatus(StatusInterface::STATUS_EXPIRED);

            return;
        }

        // if you try to set status as Active
        if (
            isset($changeSet[StatusInterface::PROPERTY_NAME_STATUS]) &&
            $entity->getStatus() == StatusInterface::STATUS_ACTIVE
        ) {
            // set Cancel if you try to set Active status for more than one entity
            // only first entity in this persistence can be with Active status
            if ($this->entityToSetStatusAsActive) {
                $entity->setStatus(StatusInterface::STATUS_CANCELED);
            } else {
                // cancel previous active records (from database)
                $baseEntities = $em->getRepository(get_class($entity))->findBy([
                    DatetimePeriodStatusInterface::PROPERTY_NAME_BUSINESS_PROFILE => $entity->getBusinessProfile(),
                    StatusInterface::PROPERTY_NAME_STATUS => StatusInterface::STATUS_ACTIVE
                ]);

                $uow = $em->getUnitOfWork();

                foreach ($baseEntities as $baseEntity) {
                    /** @var DatetimePeriodStatusInterface $baseEntity*/
                    $baseEntity->setStatus(StatusInterface::STATUS_CANCELED);

                    // add
                    $uow->propertyChanged(
                        $baseEntity,
                        StatusInterface::PROPERTY_NAME_STATUS,
                        $baseEntity->getStatus(),
                        StatusInterface::STATUS_CANCELED
                    );

                    $uow->scheduleExtraUpdate($baseEntity, [
                        StatusInterface::PROPERTY_NAME_STATUS => [
                            $baseEntity->getStatus(),
                            StatusInterface::STATUS_CANCELED
                        ]
                    ]);

                    $em->persist($baseEntity);
                }

                $this->entityToSetStatusAsActive = $entity;
            }
        }
    }

    /**
     * Set Free plan subscription for businesses without subscription
     *
     * @param EntityManager $em
     * @return \Domain\BusinessBundle\Entity\BusinessProfile[]|null
     */
    public function setFreeSubscription(EntityManager $em)
    {
        $businessProfiles = $em
            ->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getBusinessWithoutActiveSubscription();

        foreach ($businessProfiles as $businessProfile) {
            $freeSubscriptionPlan = $em
                ->getRepository('DomainBusinessBundle:SubscriptionPlan')
                ->findOneBy(['code' => SubscriptionPlanInterface::CODE_FREE]);

            $startDate = new \DateTime();
            $endDate = new \DateTime();
            $endDate->modify('+1 year');

            $subscription = new Subscription();
            $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);
            $subscription->setBusinessProfile($businessProfile);
            $subscription->setSubscriptionPlan($freeSubscriptionPlan);
            $subscription->setStartDate($startDate);
            $subscription->setEndDate($endDate);

            $em->persist($subscription);
        }

        return $businessProfiles;
    }
}
