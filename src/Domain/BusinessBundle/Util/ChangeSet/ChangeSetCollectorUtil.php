<?php

namespace Domain\BusinessBundle\Util\ChangeSet;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Model\DataType\ChangeSetArrayDTO;
use Domain\BusinessBundle\Model\DataType\ChangeSetCollectionDTO;
use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilePropertyAccessorUtil;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\DoctrineUtil;
use Domain\BusinessBundle\Entity\Category;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\VideoBundle\Entity\VideoMedia;
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
     * @param Collection $oldCategories
     * @param array      $oldImages
     * @return array
     */
    public static function getEntityCollectionsChangeSet(
        EntityManagerInterface $em,
        $entity,
        $oldCategories,
        $oldImages
    ) : array {
        $entityCollections = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertiesHavingCollectionType(
            $em,
            $entity
        );

        $changeEntries = [];

        foreach ($entityCollections as $className => $property) {
            if ($className === Category::class) {
                $changeEntry = ChangeSetCollectorUtil::getChangeSetEntryForCategories(
                    $entity,
                    $property,
                    $className,
                    $oldCategories
                );

                if ($changeEntry !== false) {
                    $changeEntries[] = $changeEntry;
                }
            } elseif ($className !== BusinessGallery::class) {
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
                    $className,
                    $oldImages
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
     * @param $oldImages
     * @return array
     */
    public static function getChangeSetEntryForImages(
        EntityManagerInterface $em,
        $entity,
        $property,
        $className,
        $oldImages
    ) {
        $collection = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertyValue($entity, $property);
        $insertDiffVar = $collection->getInsertDiff();
        $deleteDiffVar = $collection->getDeleteDiff();

        $insertDiff = [];
        $deleteDiff = [];

        $changeset = [];
        /** @var BusinessGallery $newBusinessGalleryObject */
        foreach ($insertDiffVar as $idFromPost => $newBusinessGalleryObject) {
            if (self::checkDuplicatesInsertDelete($deleteDiff, $newBusinessGalleryObject)) {
                continue;
            }
            $changeset[] = self::buildChangeSetEntryObject(
                $property,
                '',
                ChangeSetSerializerUtil::serializeBusinessGalleryObject($newBusinessGalleryObject),
                ChangeSetCalculator::IMAGE_ADD,
                Media::class
            );
            $insertDiff[$idFromPost] = $newBusinessGalleryObject;
        }

        /** @var BusinessGallery $removedBusinessGalleryObject */
        foreach ($deleteDiffVar as $removedBusinessGalleryObject) {
            if (self::checkDuplicatesInsertDelete($insertDiff, $removedBusinessGalleryObject, true)) {
                continue;
            }
            $changeset[] = self::buildChangeSetEntryObject(
                $property,
                ChangeSetSerializerUtil::serializeBusinessGalleryObject($removedBusinessGalleryObject),
                '',
                ChangeSetCalculator::IMAGE_REMOVE,
                $className
            );
            $deleteDiff[] = $removedBusinessGalleryObject;
        }

        //check gallery properties update
        foreach ($collection as $businessGallery) {
            $id = $businessGallery->getId();

            if (isset($oldImages[$id])) {
                $oldValue = [];
                $newValue = [];

                //check update of gallery type
                if ($oldImages[$id]['type'] != $businessGallery->getType()) {
                    $oldValue[$id]['type'] = $oldImages[$id]['type'];
                    $newValue[$id]['type'] = $businessGallery->getType();
                }

                //check update of gallery decription
                if ($oldImages[$id]['description'] != $businessGallery->getDescription()) {
                    $oldValue[$id]['description'] = $oldImages[$id]['description'];
                    $newValue[$id]['description'] = $businessGallery->getDescription();
                }

                if ($newValue) {
                    //build change set
                    $changeset[] = self::buildChangeSetEntryObject(
                        $property,
                        (new ChangeSetArrayDTO($oldValue))->getJSONContent(),
                        (new ChangeSetArrayDTO($newValue))->getJSONContent(),
                        ChangeSetCalculator::PROPERTY_IMAGE_PROPERTY_UPDATE,
                        $className
                    );
                }
            }
        }

        return $changeset;
    }

    protected function checkDuplicatesInsertDelete($collectionEntryies, $gallery, $useCollectionKeysAsId = false)
    {
        foreach ($collectionEntryies as $id => $entry) {
            if ((!$useCollectionKeysAsId && $gallery->getMedia()->getId() == $entry->getMedia()->getId())
                    ||
                ($useCollectionKeysAsId && $id == $gallery->getId())
            ) {
                return true;
            }
        }
        return false;
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
            if (!is_array($change) || $field == 'video' || $field == BusinessProfile::BUSINESS_PROFILE_FIELD_LOGO ||
                $field == BusinessProfile::BUSINESS_PROFILE_FIELD_BACKGROUND
            ) {
                continue;
            }

            if ($change[0] !== null && $change[1] !== null) {
                $action = ChangeSetCalculator::PROPERTY_CHANGE;
            } elseif ($change[0] === null) {
                $action = ChangeSetCalculator::PROPERTY_ADD;
            } else {
                $action = ChangeSetCalculator::PROPERTY_REMOVE;
            }

            
            if (!is_object($change[0]) && !is_object($change[1]) && $change[0] == $change[1]) {
                continue;
            }

            if (is_object($change[0]) && is_object($change[1])) {
                $oldValue = (new ChangeSetCollectionDTO([$change[0]]))->getJSONContent();
                $newValue = (new ChangeSetCollectionDTO([$change[1]]))->getJSONContent();
                $class = get_class($change[1]);
            } else {
                $oldValue = $change[0] ?? '-';
                $newValue = $change[1] ?? '-';
                $class = null;
            }

            $changeEntries[] = self::buildChangeSetEntryObject($field, $oldValue, $newValue, $action, $class);
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
            } elseif ($diff[1] == null) {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[0]));
                $entry->setNewValue('');
                $entry->setAction(ChangeSetCalculator::VIDEO_REMOVE);
            } elseif ($diff[1]->getId() != $diff[0]->getId()) {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[0]));
                $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[1]));
                $entry->setAction(ChangeSetCalculator::VIDEO_UPDATE);
            } elseif ($diff[0]->getTitle() != $diff[1]->getTitle() or
                $diff[0]->getDescription() != $diff[1]->getDescription()
            ) {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[0]));
                $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileVideo($diff[1]));
                $entry->setAction(ChangeSetCalculator::VIDEO_PROPERTY_UPDATE);
            } else {
                return false;
            }

            if ($entry) {
                $entry->setClassName(VideoMedia::class);
            }

            return $entry;
        }

        return false;
    }

    public static function getEntityMediaItemChangeSet($em, $entity, $type)
    {
        switch ($type) {
            case BusinessProfile::BUSINESS_PROFILE_FIELD_LOGO:
                $addAction    = ChangeSetCalculator::LOGO_ADD;
                $removeAction = ChangeSetCalculator::LOGO_REMOVE;
                $updateAction = ChangeSetCalculator::LOGO_UPDATE;
                break;

            case BusinessProfile::BUSINESS_PROFILE_FIELD_BACKGROUND:
                $addAction    = ChangeSetCalculator::BACKGROUND_ADD;
                $removeAction = ChangeSetCalculator::BACKGROUND_REMOVE;
                $updateAction = ChangeSetCalculator::BACKGROUND_UPDATE;
                break;

            default:
                return false;
        }

        try {
            $profileDiff = DoctrineUtil::diffDoctrineObject($em, $entity);
        } catch (ContextErrorException $e) {
            return false;
        }

        $entry = new ChangeSetEntry();
        $entry->setFieldName($type);

        if (!isset($profileDiff[$type])) {
            return false;
        }

        $diff = $profileDiff[$type];

        if (is_array($diff) && count($diff) == 2) {
            if ($diff[0] == null) {
                $entry->setOldValue('');
                $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileMediaItem($diff[1]));
                $entry->setAction($addAction);
            } elseif ($diff[1] == null) {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileMediaItem($diff[0]));
                $entry->setNewValue('');
                $entry->setAction($removeAction);
            } elseif ($diff[1]->getId() != $diff[0]->getId()) {
                $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileMediaItem($diff[0]));
                $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileMediaItem($diff[1]));
                $entry->setAction($updateAction);
            } else {
                return false;
            }

            return $entry;
        }

        return false;
    }

    /**
     * @param $entity
     * @param $property
     * @param $className
     * @param Collection $originalCollection
     * @return bool|ChangeSetEntry
     */
    public static function getChangeSetEntryForCategories($entity, $property, $className, $originalCollection)
    {
        $collection = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertyValue(
            $entity,
            $property
        );

        if (self::checkCollectionUpdated($originalCollection, $collection)) {
            $collection = BusinessProfilePropertyAccessorUtil::getBusinessProfilePropertyValue(
                $entity,
                $property
            );

            $newValue = (new ChangeSetCollectionDTO($collection))->getJSONContent();

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

    private static function checkCollectionUpdated($originalCollection, $collection)
    {
        $oldIds = self::getCollectionKeys($originalCollection);
        $newIds = self::getCollectionKeys($collection);

        return array_diff($oldIds, $newIds) or array_diff($newIds, $oldIds);
    }

    private static function getCollectionKeys($collection)
    {
        $ids = [];

        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }
}
