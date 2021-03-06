<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Model\StatusInterface;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\BusinessBundle\Util\Traits\StatusTrait;

/**
 * Class SubscriptionStatusManager
 * @package Domain\BusinessBundle\Manager
 */
class SubscriptionStatusManager
{
    /**
     * @var Subscription[]|null
     */
    private $bulkSubscriptions;

    /**
     * @param Subscription $entity
     * @param EntityManager $em
     */
    public function manageDatetimePeriodStatus(Subscription $entity, EntityManager $em)
    {
        // exclude subscription that were created with business
        if ($entity->getBusinessProfile() and $entity->getBusinessProfile()->getId()) {
            //get all active or pending subscription

            $baseEntities = $this->getBusinessActualSubscriptions($entity->getBusinessProfile(), true);

            //store batch entities insert/update
            $this->bulkSubscriptions[] = $entity;

            $baseEntities = $this->getSubscriptionsArrayForPriorityCalculation($baseEntities, $this->bulkSubscriptions);

            // get priority subscription
            $priorityEntity = $this->getPrioritySubscription($baseEntities);

            // $priorityEntity == null - means there is no available subscription, see SubscriptionListener

            $uow = $em->getUnitOfWork();

            foreach ($baseEntities as $baseEntity) {
                /** @var Subscription $baseEntity */
                if ($baseEntity->isExpired()) {
                    // disable expired subscription
                    $this->updateSubscriptionStatus($baseEntity, StatusInterface::STATUS_EXPIRED, $uow);
                } elseif ($baseEntity == $priorityEntity) {
                    // enable priority subscription (only pending subscription can be activated)
                    if ($baseEntity->getStatus() == StatusInterface::STATUS_PENDING) {
                        $this->updateSubscriptionStatus($baseEntity, StatusInterface::STATUS_ACTIVE, $uow);
                    }
                } else {
                    // set pending status to all other active subscriptions
                    if ($baseEntity->getStatus() == StatusInterface::STATUS_ACTIVE) {
                        $this->updateSubscriptionStatus($baseEntity, StatusInterface::STATUS_PENDING, $uow);
                    }
                }
            }
        }
    }

    /**
     * @param Subscription[] $baseEntities
     * @param Subscription[] $subscriptions
     *
     * @return Subscription[]
     */
    protected function getSubscriptionsArrayForPriorityCalculation($baseEntities, $subscriptions)
    {
        foreach ($subscriptions as $entity) {
            if (!$this->searchSubscriptionInArray($entity, $subscriptions)) {
                $baseEntities[] = $entity;
            }
        }

        return $baseEntities;
    }

    /**
     * @param Subscription   $subscription
     * @param Subscription[] $array
     *
     * @return Subscription[]
     */
    protected function searchSubscriptionInArray($subscription, $array)
    {
        foreach ($array as $item) {
            if ($item == $subscription) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Subscription $subscription
     * @param string $status
     * @param \Doctrine\ORM\UnitOfWork $uow
     */
    protected function updateSubscriptionStatus(Subscription $subscription, $status, \Doctrine\ORM\UnitOfWork $uow)
    {
        $uow->propertyChanged(
            $subscription,
            StatusInterface::PROPERTY_NAME_STATUS,
            $subscription->getStatus(),
            $status
        );

        $uow->scheduleExtraUpdate($subscription, [
            StatusInterface::PROPERTY_NAME_STATUS => [
                $subscription->getStatus(),
                $status
            ]
        ]);
    }

    /**
     * @param Subscription[] $entities
     *
     * @return Subscription|null
     */
    public function getPrioritySubscription($entities)
    {
        $priorityEntity = null;

        if ($entities) {
            // default subscription rank
            $maxRank = SubscriptionPlanInterface::CODE_FREE;
            $now = new \DateTime();

            foreach ($entities as $entity) {
                if (!$entity->isExpired() and in_array($entity->getStatus(), StatusTrait::getActualStatuses())) {
                    $rank = $entity->getSubscriptionPlan() ? $entity->getSubscriptionPlan()->getCode() : null;

                    if ($rank !== null and $rank >= $maxRank) {
                        $endDate = $entity->getEndDate();

                        if ($endDate > $now) {
                            $maxRank = $rank;
                            $priorityEntity = $entity;
                        }
                    }
                }
            }
        }

        return $priorityEntity;
    }

    /**
     * Set Free plan subscription for businesses without subscription
     *
     * @param EntityManager $em
     * @return \Domain\BusinessBundle\Entity\BusinessProfile[]|null
     */
    public function setFreeSubscription(EntityManager $em)
    {
        $businessProfiles = $em->getRepository(BusinessProfile::class)->getBusinessWithoutActiveSubscription();

        foreach ($businessProfiles as $businessProfile) {
            $freeSubscriptionPlan = $em->getRepository(SubscriptionPlan::class)
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

    /**
     * Update Free plan subscription for businesses without subscription
     *
     * @param BusinessProfile $businessProfile
     * @param EntityManager $em
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function updateBusinessProfileFreeSubscription(BusinessProfile $businessProfile, EntityManager $em)
    {
        $currentSubscriptions = $businessProfile->getSubscriptions();

        /* @var $currentSubscriptions Subscription[] */
        foreach ($currentSubscriptions as $item) {
            if ($item->getStatus() === StatusInterface::STATUS_ACTIVE) {
                return $businessProfile;
            }
        }

        $businessProfile = $this->setBusinessProfileFreeSubscription($businessProfile, $em);

        return $businessProfile;
    }

    /**
     * Set Free plan subscription for businesses without subscription
     *
     * @param BusinessProfile $businessProfile
     * @param EntityManager $em
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function setBusinessProfileFreeSubscription(BusinessProfile $businessProfile, EntityManager $em)
    {
        $freeSubscriptionPlan = $em->getRepository(SubscriptionPlan::class)
            ->findOneBy(['code' => SubscriptionPlanInterface::CODE_FREE]);

        $startDate = new \DateTime();
        $endDate   = new \DateTime();
        $endDate->modify('+1 year');

        $subscription = new Subscription();
        $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);
        $subscription->setBusinessProfile($businessProfile);
        $subscription->setSubscriptionPlan($freeSubscriptionPlan);
        $subscription->setStartDate($startDate);
        $subscription->setEndDate($endDate);

        $em->persist($subscription);

        return $businessProfile;
    }

    /**
     * Set Free plan subscription for businesses without subscription
     *
     * @param BusinessProfile $entity
     * @param EntityManager $em
     * @return Subscription|null
     */
    public function manageBusinessSubscriptionCreate(BusinessProfile $entity, EntityManager $em)
    {
        $subscriptions = $this->getBusinessActualSubscriptions($entity);

        if (!$subscriptions) {
            $freeSubscriptionPlan = $em->getRepository(SubscriptionPlan::class)
                ->findOneBy(['code' => SubscriptionPlanInterface::CODE_FREE]);

            $startDate = new \DateTime();
            $endDate   = new \DateTime();
            $endDate->modify('+1 year');

            $subscription = new Subscription();
            $subscription->setStatus(DatetimePeriodStatusInterface::STATUS_ACTIVE);
            $subscription->setBusinessProfile($entity);
            $subscription->setSubscriptionPlan($freeSubscriptionPlan);
            $subscription->setStartDate($startDate);
            $subscription->setEndDate($endDate);

            $em->persist($subscription);
            // EntityManager#flush is not allowed during onFlush event

            return $subscription;
        }

        return null;
    }

    /**
     * Disable subscription if there are several of active subscription for 1 business
     *
     * @param BusinessProfile $entity
     * @param EntityManager $em
     * @return int
     */
    public function manageBusinessSubscriptionExcess(BusinessProfile $entity, EntityManager $em)
    {
        $updatedRecordsCount = 0;

        $actualSubscriptions = $this->getBusinessActualSubscriptions($entity);

        if ($actualSubscriptions) {
            $prioritySubscription = $this->getPrioritySubscription($actualSubscriptions);

            if ($prioritySubscription) {
                foreach ($actualSubscriptions as $subscription) {
                    if ($subscription->getId() != $prioritySubscription->getId()) {
                        $subscription->setStatus(StatusInterface::STATUS_PENDING);
                        $updatedRecordsCount ++;
                    }
                }
            }
        }

        return $updatedRecordsCount;
    }

    /**
     * Get business actual subscriptions
     *
     * @param BusinessProfile $entity
     * @param bool            $allowExpired
     *
     * @return Subscription[]
     */
    protected function getBusinessActualSubscriptions(BusinessProfile $entity, $allowExpired = false)
    {
        $subscriptions = [];

        $data = $entity->getSubscriptions();

        foreach ($data as $item) {
            /* @var $item Subscription */
            if (in_array($item->getStatus(), StatusTrait::getActualStatuses())) {
                if ($allowExpired or !$item->isExpired()) {
                    $subscriptions[] = $item;
                }
            }
        }

        return $subscriptions;
    }
}
