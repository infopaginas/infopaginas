<?php

namespace Domain\BusinessBundle\Util\Task;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;

/**
 * Class TranslationChangeSetUtil
 * @package Domain\BusinessBundle\Util\Task
 */
class TranslationChangeSetUtil
{
    /**
     * Prepare business profile translation collection
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

        $phones = json_decode($change->getNewValue());
        if ($phones) {
            foreach ($phones as $item) {
                if (!$item->id) {
                    $phone = new BusinessProfileTranslation();
                    $phone->setPhone($item->value);
                    $phone->setBusinessProfile($businessProfile);
                    $entityManager->persist($phone);
                } else {
                    $phone = $entityManager->getRepository(BusinessProfilePhone::class)->find($item->id);
                    $phone->setPhone($item->value);
                }

                $collection->add($phone);
            }
        }

        return $collection;
    }
}
