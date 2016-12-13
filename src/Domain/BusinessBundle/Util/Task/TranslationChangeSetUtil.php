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
    public static function getTranslationCollectionsFromChangeSet(
        ChangeSetEntry $change,
        BusinessProfile $businessProfile,
        EntityManagerInterface $entityManager
    ) : ArrayCollection {
        $collection = new ArrayCollection();

        $translations = json_decode($change->getNewValue());
        if ($translations) {
            foreach ($translations as $item) {
                $value = json_decode($item->value);

                if (!$item->id) {
                    $translation = $entityManager->getRepository(BusinessProfileTranslation::class)->findOneBy(
                        [
                            'locale' => $value->locale,
                            'field'  => $value->field,
                            'object' => $businessProfile,
                        ]
                    );

                    if (!$translation) {
                        $translation = new BusinessProfileTranslation();
                    }

                    $translation->setField($value->field);
                    $translation->setLocale($value->locale);
                    $translation->setContent($value->value);
                    $translation->setObject($businessProfile);
                    $entityManager->persist($translation);
                } else {
                    $translation = $entityManager->getRepository(BusinessProfileTranslation::class)->find($item->id);
                    $translation->setContent($value->value);
                }

                $collection->add($translation);
            }
        }

        return $collection;
    }
}
