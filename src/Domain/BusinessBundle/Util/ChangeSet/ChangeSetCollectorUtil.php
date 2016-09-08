<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 31.08.16
 * Time: 22:44
 */

namespace Domain\BusinessBundle\Util\ChangeSet;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Model\DataType\ChangeSetCollectionDTO;
use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilePropertyAccessorUtil;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\DoctrineUtil;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class ChangeSetCollectorUtil
 * @package Domain\BusinessBundle\Util\ChangeSet
 */
class ChangeSetCollectorUtil
{
    /**
     * @param EntityManagerInterface $em
     * @param $entity
     * @return array
     */
    public static function getEntityCollectionsChangeSet(EntityManagerInterface $em, $entity) : array
    {
        $entityCollections = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertiesHavingCollectionType(
            $em,
            $entity
        );

        $changeEntries = [];

        foreach ($entityCollections as $className => $property) {
            if ($className !== BusinessGallery::class) {
                $changeEntry = ChangeSetCollectorUtil::getChangeSetEntryForRegularCollection(
                    $entity,
                    $property,
                    $className
                );

                if ($changeEntry !== false) {
                    $changeEntries[] = $changeEntry;
                }
            } else {
                $imagesChanges = ChangeSetCollectorUtil::getChangeSetEntryForImages(
                    $em,
                    $entity,
                    $property,
                    $className
                );

                $changeEntries = array_merge($changeEntries, $imagesChanges);
            }
        }

        return $changeEntries;
    }

    /**
     * @param $entity
     * @param $property
     * @param $className
     * @return bool|ChangeSetEntry
     */
    public static function getChangeSetEntryForRegularCollection($entity, $property, $className)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $persistentCollection = $accessor->getValue($entity, $property);

        $insertDiff = $persistentCollection->getInsertDiff();
        $deleteDiff = $persistentCollection->getDeleteDiff();

        if (!empty($insertDiff) || !empty($deleteDiff)) {
            $collection = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertyValue(
                $entity,
                $property
            );

            $newValue = (new ChangeSetCollectionDTO($collection))->getJSONContent();

            $originalCollection = BusinessProfilePropertyAccessorUtil::getOriginalBusinessProfileCollectionValues(
                $entity,
                $property,
                $insertDiff,
                $deleteDiff
            );

            $oldValue = (new ChangeSetCollectionDTO($originalCollection))
                ->getJSONContent();

            $changeEntry = self::buildChangeSetEntryObject(
                $property,
                $oldValue,
                $newValue,
                ChangeSetCalculator::PROPERTY_CHANGE,
                $className
            );

            return $changeEntry;
        }

        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @param $entity
     * @param $property
     * @param $className
     * @return array
     */
    public static function getChangeSetEntryForImages(EntityManagerInterface $em, $entity, $property, $className)
    {
        $collection = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertyValue($entity, $property);

        $insertDiff = $collection->getInsertDiff();
        $deleteDiff = $collection->getDeleteDiff();

        $changeset = [];

        /** @var BusinessGallery $newBusinessGalleryObject */
        foreach ($insertDiff as $newBusinessGalleryObject) {
            $changeset[] = self::buildChangeSetEntryObject(
                $property,
                '',
                ChangeSetSerializerUtil::serializeBusinessGalleryObject($newBusinessGalleryObject),
                ChangeSetCalculator::IMAGE_ADD,
                Media::class
            );
        }

        /** @var BusinessGallery $removedBusinessGalleryObject */
        foreach ($deleteDiff as $removedBusinessGalleryObject) {
            $changeset[] = self::buildChangeSetEntryObject(
                $property,
                ChangeSetSerializerUtil::serializeBusinessGalleryObject($removedBusinessGalleryObject),
                '',
                ChangeSetCalculator::IMAGE_REMOVE,
                $className
            );
        }

        $originalCollection = BusinessProfilePropertyAccessorUtil::getOriginalBusinessProfileCollectionValues(
            $entity,
            $property,
            $insertDiff,
            $deleteDiff
        );

        foreach ($originalCollection as $businessGallery) {
            try {
                $changeset[] = self::buildChangeSetEntryObject(
                    $property,
                    '',
                    ChangeSetSerializerUtil::serializeBusinessGalleryDiff($em, $businessGallery),
                    ChangeSetCalculator::IMAGE_UPDATE,
                    $className
                );
            } catch (ContextErrorException $e) {
                continue;
            }
        }

        return $changeset;
    }

    /**
     * @param $em
     * @param $entity
     * @return array
     */
    public static function getEntityFieldsChangeSet($em, $entity) : array
    {
        $fieldsChanges = DoctrineUtil::diffDoctrineObject($em, $entity);

        $changeEntries = [];

        foreach ($fieldsChanges as $field => $change) {
            if (!is_array($change) || $field == 'video') {
                continue;
            }

            if ($change[0] !== null && $change[1] !== null) {
                $action = ChangeSetCalculator::PROPERTY_CHANGE;
            } elseif ($change[0] === null) {
                $action = ChangeSetCalculator::PROPERTY_ADD;
            } else {
                $action = ChangeSetCalculator::PROPERTY_REMOVE;
            }

            $oldValue = $change[0] === null ? '-' : $change[0];
            $newValue = $change[1] === null ? '-' : $change[1];

            $changeEntries[] = self::buildChangeSetEntryObject($field, $oldValue, $newValue, $action);
        }

        return $changeEntries;
    }

    /**
     * @param $em
     * @param $entity
     * @return bool|ChangeSetEntry
     */
    public static function getEntityVideoChangeSet($em, $entity)
    {
        try {
            $profileDiff = DoctrineUtil::diffDoctrineObject($em, $entity);
        } catch (ContextErrorException $e) {
            return false;
        }

        $entry = new ChangeSetEntry();
        $entry->setFieldName('video');

        if (!isset($profileDiff['video'])) {
            
            if (!$entity->getVideo()) {
                return false;
            }

            try {
                $videoDiff = DoctrineUtil::diffDoctrineObject($em, $entity->getVideo());
            } catch (ContextErrorException $e) {
                return false;
            }

            if (!empty($videoDiff)) {
                $entry->setOldValue('');
                $entry->setNewValue(ChangeSetSerializerUtil::serializeObject($videoDiff));
                $entry->setAction(ChangeSetCalculator::VIDEO_UPDATE);
                return $entry;
            }

            return false;
        }

        $diff = $profileDiff['video'];

        if (is_array($diff) && count($diff) == 2) {
            if ($diff[0] == null) {
                $entry->setOldValue('');
                $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[1]));
                $entry->setAction(ChangeSetCalculator::VIDEO_ADD);
            } elseif($diff[1] == null) {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[0]));
                $entry->setNewValue('');
                $entry->setAction(ChangeSetCalculator::VIDEO_REMOVE);
            } else {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[0]));
                $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[1]));
                $entry->setAction(ChangeSetCalculator::VIDEO_UPDATE);
            }

            return $entry;
        }

        return false;
    }

    /**
     * @param $fieldName
     * @param $oldValue
     * @param $newValue
     * @param $action
     * @param string $class
     * @return ChangeSetEntry
     */
    private static function buildChangeSetEntryObject($fieldName, $oldValue, $newValue, $action, $class = '')
    {
        $entry = new ChangeSetEntry();
        $entry->setFieldName($fieldName);
        $entry->setOldValue($oldValue);
        $entry->setNewValue($newValue);
        $entry->setAction($action);
        $entry->setClassName($class);

        return $entry;
    }
}
