<?php

namespace Domain\BusinessBundle\Util\Task;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\ChangeSetEntry;

/**
 * Class PhoneChangeSetUtil
 * @package Domain\BusinessBundle\Util\Task
 */
class PhoneChangeSetUtil
{
    /**
     * Prepare business profile phones collection
     *
     * @access public
     * @param ChangeSetEntry $change
     * @param BusinessProfile $businessProfile
     * @param EntityManagerInterface $entityManager
     * @return ArrayCollection
     */
    public static function getPhonesCollectionsFromChangeSet(
        ChangeSetEntry $change,
        BusinessProfile $businessProfile,
        EntityManagerInterface $entityManager
    ) : ArrayCollection {
        $collection = new ArrayCollection();

        $phones  = json_decode($change->getNewValue());
        $dataOld = json_decode($change->getOldValue());

        if ($dataOld) {
            foreach ($dataOld as $key => $itemOld) {
                if (!empty($phones[$key])) {
                    $phones[$key]->id = $itemOld->id;
                }
            }
        }

        if ($phones) {
            foreach ($phones as $item) {
                $data = json_decode($item->value);

                if (!$item->id) {
                    $phone = new BusinessProfilePhone();
                    $phone->setBusinessProfile($businessProfile);

                    $entityManager->persist($phone);
                } else {
                    $phone = $entityManager->getRepository(BusinessProfilePhone::class)->find($item->id);
                }

                $phone->setPhone($data->value);
                $phone->setType($data->type);
                $phone->setPriority(BusinessProfilePhone::getPriorityByType($data->type));

                $collection->add($phone);
            }
        }

        return $collection;
    }
}
