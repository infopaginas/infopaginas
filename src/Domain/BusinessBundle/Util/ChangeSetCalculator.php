<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 27.08.16
 * Time: 17:17
 */

namespace Domain\BusinessBundle\Util;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Util\ChangeSet\ChangeSetCollectorUtil;

/**
 * Class ChangeSetCalculator
 * @package Domain\BusinessBundle\Util
 */
class ChangeSetCalculator
{
    const PROPERTY_CHANGE = 'PROPERTY_CHANGE';
    const PROPERTY_ADD    = 'PROPERTY_ADD';
    const PROPERTY_REMOVE = 'PROPERTY_REMOVE';

    const IMAGE_ADD    = 'IMAGE_ADD';
    const IMAGE_REMOVE = 'IMAGE_REMOVE';
    const IMAGE_UPDATE = 'IMAGE_UPDATE';

    const VIDEO_ADD    = 'VIDEO_ADD';
    const VIDEO_REMOVE = 'VIDEO_REMOVE';
    const VIDEO_UPDATE = 'VIDEO_UPDATE';

    /**
     * @param EntityManagerInterface $em
     * @param $entity
     * @param Collection $oldCategories
     * @return ChangeSet
     */
    public static function getChangeSet(EntityManagerInterface $em, $entity, $oldCategories) : ChangeSet
    {
        $collectionsChangeSet = ChangeSetCollectorUtil::getEntityCollectionsChangeSet($em, $entity, $oldCategories);
        $fieldsChangeSet      = ChangeSetCollectorUtil::getEntityFieldsChangeSet($em, $entity);
        $videoChange          = ChangeSetCollectorUtil::getEntityVideoChangeSet($em, $entity);

        $changeSet = new ChangeSet();

        /** @var ChangeSetEntry $entry */
        foreach ($fieldsChangeSet as $entry) {
            $entry->setChangeSet($changeSet);
            $changeSet->addEntry($entry);
            $em->persist($entry);
        }

        /** @var ChangeSetEntry $entry */
        foreach ($collectionsChangeSet as $entry) {
            $entry->setChangeSet($changeSet);
            $changeSet->addEntry($entry);
            $em->persist($entry);
        }

        if ($videoChange !== false) {
            $videoChange->setChangeSet($changeSet);
            $changeSet->addEntry($videoChange);
            $em->persist($videoChange);
        }

        $em->persist($changeSet);

        return $changeSet;
    }
}
