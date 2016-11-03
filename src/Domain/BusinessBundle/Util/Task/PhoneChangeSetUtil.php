<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 21.09.16
 * Time: 15:54
 */

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

        foreach (json_decode($change->getNewValue()) as $item) {
            if (!$item->id) {
                $phone = new BusinessProfilePhone();
                $phone->setPhone($item->value);
                $phone->setBusinessProfile($businessProfile);
                $entityManager->persist($phone);
            } else {
                $phone = $entityManager->getRepository(BusinessProfilePhone::class)->find($item->id);
                $phone->setPhone($item->value);
            }

            $collection->add($phone);
        }

        return $collection;
    }
}
