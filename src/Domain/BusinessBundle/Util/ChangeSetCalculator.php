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
use Domain\BusinessBundle\Entity\BusinessProfile;
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

    const PROPERTY_IMAGE_PROPERTY_UPDATE = 'PROPERTY_IMAGE_PROPERTY_UPDATE';

    const IMAGE_ADD    = 'IMAGE_ADD';
    const IMAGE_REMOVE = 'IMAGE_REMOVE';

    const VIDEO_ADD    = 'VIDEO_ADD';
    const VIDEO_REMOVE = 'VIDEO_REMOVE';
    const VIDEO_UPDATE = 'VIDEO_UPDATE';
    const VIDEO_PROPERTY_UPDATE = 'VIDEO_PROPERTY_UPDATE';

    const LOGO_ADD    = 'LOGO_ADD';
    const LOGO_REMOVE = 'LOGO_REMOVE';
    const LOGO_UPDATE = 'LOGO_UPDATE';

    const BACKGROUND_ADD    = 'BACKGROUND_ADD';
    const BACKGROUND_REMOVE = 'BACKGROUND_REMOVE';
    const BACKGROUND_UPDATE = 'BACKGROUND_UPDATE';

    /**
     * @param EntityManagerInterface $em
     * @param $entity
     * @param Collection $oldCategories
     * @param array      $oldImages
     * @return ChangeSet
     */
    public static function getChangeSet(EntityManagerInterface $em, $entity, $oldCategories, $oldImages) : ChangeSet
    {
        $collectionsChangeSet = ChangeSetCollectorUtil::getEntityCollectionsChangeSet(
            $em,
            $entity,
            $oldCategories,
            $oldImages
        );
        $fieldsChangeSet      = ChangeSetCollectorUtil::getEntityFieldsChangeSet($em, $entity);
        $videoChange          = ChangeSetCollectorUtil::getEntityVideoChangeSet($em, $entity);
        $logoChange           = ChangeSetCollectorUtil::getEntityMediaItemChangeSet(
            $em,
            $entity,
            BusinessProfile::BUSINESS_PROFILE_FIELD_LOGO
        );
        $backgroundChange     = ChangeSetCollectorUtil::getEntityMediaItemChangeSet(
            $em,
            $entity,
            BusinessProfile::BUSINESS_PROFILE_FIELD_BACKGROUND
        );

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

        if ($logoChange !== false) {
            $logoChange->setChangeSet($changeSet);
            $changeSet->addEntry($logoChange);
            $em->persist($logoChange);
        }

        if ($backgroundChange !== false) {
            $backgroundChange->setChangeSet($changeSet);
            $changeSet->addEntry($backgroundChange);
            $em->persist($backgroundChange);
        }

        $em->persist($changeSet);

        return $changeSet;
    }
}
