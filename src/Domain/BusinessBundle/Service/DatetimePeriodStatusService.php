<?php

namespace Domain\BusinessBundle\Service;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Manager\SubscriptionStatusManager;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;

class DatetimePeriodStatusService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SubscriptionStatusManager $subscriptionStatusManager
     */
    private $subscriptionStatusManager;

    /**
     * @param EntityManager $em
     * @param SubscriptionStatusManager $subscriptionStatusManager
     */
    public function __construct(EntityManager $em, SubscriptionStatusManager $subscriptionStatusManager)
    {
        $this->em = $em;
        $this->subscriptionStatusManager = $subscriptionStatusManager;
    }

    /**
     * Set Expired status if it's
     *
     * @return int
     */
    public function updateStatus()
    {
        $updatedRecordsCount = 0;

        $batchSize = 20;
        $i = 0;

        $data = $this->em->getRepository(Subscription::class)->getActiveSubscriptionsStepIterator();

        foreach ($data as $row) {
            /* @var $entity Subscription */
            $entity = $row[0];

            if ($entity->isExpired()) {
                $entity->setStatus(DatetimePeriodStatusInterface::STATUS_EXPIRED);

                $updatedRecordsCount++;

                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $i ++;
            }
        }

        if ($updatedRecordsCount) {
            $this->em->flush();
        }

        return $updatedRecordsCount;
    }

    /**
     * @return int
     */
    public function createActiveSubscriptions()
    {
        $createdRecordsCount = 0;

        $batchSize = 20;
        $i = 0;

        $businessProfilesIterator = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getBusinessesWithoutActiveSubscriptionIterator();

        foreach ($businessProfilesIterator as $row) {
            /* @var $entity BusinessProfile */
            $entity = $row[0];

            // create default subscription if needed
            $subscription = $this->subscriptionStatusManager->manageBusinessSubscriptionCreate($entity, $this->em);

            if ($subscription) {
                $createdRecordsCount++;

                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $i ++;
            }
        }

        if ($createdRecordsCount) {
            $this->em->flush();
        }

        return $createdRecordsCount;
    }

    /**
     * @return int
     */
    public function updateActiveSubscriptions()
    {
        $updatedRecordsCount = 0;

        $batchSize = 20;
        $i = 0;

        $businessProfilesIterator = $this->em->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getBusinessesWithMultipleActiveSubscriptionsIterator();

        foreach ($businessProfilesIterator as $row) {
            /* @var $entity BusinessProfile */
            $entity = $row[0];

            // make sure that each business has only 1 active subscription
            $result = $this->subscriptionStatusManager->manageBusinessSubscriptionExcess($entity, $this->em);

            if ($result) {
                $updatedRecordsCount ++;

                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $i ++;
            }
        }

        if ($updatedRecordsCount) {
            $this->em->flush();
        }

        return $updatedRecordsCount;
    }
}
