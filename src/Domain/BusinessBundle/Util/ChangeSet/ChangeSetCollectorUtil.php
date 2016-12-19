<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 31.08.16
 * Time: 22:44
 */

namespace Domain\BusinessBundle\Util\ChangeSet;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Model\DataType\ChangeSetCollectionDTO;
use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilePropertyAccessorUtil;
use Domain\BusinessBundle\Util\ChangeSetCalculator;
use Domain\BusinessBundle\Util\DoctrineUtil;
use Domain\BusinessBundle\Entity\Category;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
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
     * @return array
     */
    public static function getEntityCollectionsChangeSet(EntityManagerInterface $em, $entity, $oldCategories) : array
    {
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

        $originalCollection = BusinessProfilePropertyAccessorUtil::getOriginalBusinessProfileCollectionValues(
            $entity,
            $property,
            $insertDiff,
            $deleteDiff
        );

        foreach ($originalCollection as $businessGallery) {
            if ($collection->exists(
                    function($key, $entry) use ($businessGallery) {
                        return  (
                                $entry->getMedia()->getId() == $businessGallery->getMedia()->getId()
                                &&
                                $entry->getIsPrimary() == $businessGallery->getIsPrimary()
                                &&
                                $entry->getDescription() == $businessGallery->getDescription()
                                &&
                                $entry->getType() == $businessGallery->getType()
                                );
                    }
                )
            ) {
                    continue;
            }

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

    protected function checkDuplicatesInsertDelete($collectionEntryies, $gallery, $useCollectionKeysAsId = false)
    {
        foreach($collectionEntryies as $id => $entry) {
            if (!$useCollectionKeysAsId && $gallery->getMedia()->getId() == $entry->getMedia()->getId()
                    ||
                $useCollectionKeysAsId && $id == $gallery->getId()
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
            if (!is_array($change) || $field == 'video' || $field == BusinessProfile::BUSINESS_PROFILE_FIELD_LOGO || $field == BusinessProfile::BUSINESS_PROFILE_FIELD_BACKGROUND) {
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
     * @param $em
     * @param $entity
     * @return bool|ChangeSetEntry
     */
    public static function getEntityLogoAndBackgroundChangeSet($em, $entity)
    {
        try {
            $profileDiff = DoctrineUtil::diffDoctrineObject($em, $entity);
        } catch (ContextErrorException $e) {
            return false;
        }

        $fields = ['logo', 'background'];
        $context = [
            'logo'          => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO,
            'background'    => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND,
        ];

        $entries = [];
        foreach($fields as $field) {
            $entry = new ChangeSetEntry();
            $entry->setFieldName($field);

            if (!isset($profileDiff[$field])) {
                continue;
            }

            $diff = $profileDiff[$field];
            if (is_array($diff) && count($diff) == 2) {
                if ($diff[0] == null) {
                    $entry->setOldValue('');
                    $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileMedia($diff[1], $context[$field]));
                    $entry->setAction(ChangeSetCalculator::PROPERTY_IMAGE_ADD);
                } elseif($diff[1] == null) {
                    $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileMedia($diff[0], $context[$field]));
                    $entry->setNewValue('');
                    $entry->setAction(ChangeSetCalculator::PROPERTY_IMAGE_REMOVE);
                } else {
                    $entry->setOldValue(ChangeSetSerializerUtil::serializeBusinessProfileMedia($diff[0], $context[$field]));
                    $entry->setNewValue(ChangeSetSerializerUtil::serializeBusinessProfileMedia($diff[1], $context[$field]));
                    $entry->setAction(ChangeSetCalculator::PROPERTY_IMAGE_UPDATE);
                }

                $entries[] = $entry;
            }
        }

        return $entries;
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
