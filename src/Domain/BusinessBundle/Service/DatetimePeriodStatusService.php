<?php

namespace Domain\BusinessBundle\Service;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;

class DatetimePeriodStatusService
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Set Expired status if it's
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
}
