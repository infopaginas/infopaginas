<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/5/16
 * Time: 2:53 PM
 */

namespace Domain\BusinessBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Domain\BannerBundle\Entity\Campaign;
use Domain\BusinessBundle\Entity\Discount;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $entityClassArray = [
            Subscription::class,
            Discount::class,
            Campaign::class
        ];

        $updatedRecordsCount = 0;

        foreach ($entityClassArray as $entityClass) {

            $entities = $this->em->getRepository($entityClass)->findBy([
                DatetimePeriodStatusInterface::PROPERTY_NAME_STATUS => DatetimePeriodStatusInterface::STATUS_ACTIVE
            ]);

            foreach ($entities as $entity) {
                /** @var DatetimePeriodStatusInterface $entity */
                if ($entity->isExpired()) {
                    $entity->setStatus(DatetimePeriodStatusInterface::STATUS_EXPIRED);
                    $this->em->persist($entity);
                    $updatedRecordsCount++;
                }
            }
        }

        if ($updatedRecordsCount) {
            $this->em->flush();
        }

        return $updatedRecordsCount;
    }
}
