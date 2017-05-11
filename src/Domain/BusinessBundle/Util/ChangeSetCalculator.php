<?php

namespace Domain\BusinessBundle\Util;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\ChangeSet;
use Domain\BusinessBundle\Entity\ChangeSetEntry;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\Neighborhood;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\VideoBundle\Entity\VideoMedia;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    const CHANGE_COMMON_PROPERTY            = 'CHANGE_COMMON_PROPERTY';
    const CHANGE_TRANSLATION                = 'CHANGE_TRANSLATION';
    const CHANGE_RELATION_MANY_TO_ONE       = 'CHANGE_RELATION_MANY_TO_ONE';
    const CHANGE_RELATION_ONE_TO_MANY       = 'CHANGE_RELATION_ONE_TO_MANY';
    const CHANGE_RELATION_MANY_TO_MANY      = 'CHANGE_RELATION_MANY_TO_MANY';
    const CHANGE_MEDIA_RELATION_MANY_TO_ONE = 'CHANGE_MEDIA_RELATION_MANY_TO_ONE';
    const CHANGE_MEDIA_RELATION_ONE_TO_MANY = 'CHANGE_MEDIA_RELATION_ONE_TO_MANY';

    public static function getChangeSet(EntityManagerInterface $em, $entityNew, $entityOld) : ChangeSet
    {
        $commonFieldsChangeSetEntries = self::getCommonFieldsChangeSetEntries(
            $entityNew,
            $entityOld
        );
        $manyToOneRelationsChangeSetEntries = self::getManyToOneRelationsChangeSetEntries(
            $entityNew,
            $entityOld,
            $em
        );
        $oneToManyRelationsChangeSetEntries = self::getOneToManyRelationsChangeSetEntries(
            $entityNew,
            $entityOld,
            $em
        );
        $manyToManyRelationsChangeSetEntries = self::getManyToManyRelationsChangeSetEntries(
            $entityNew,
            $entityOld,
            $em
        );
        $mediaManyToOneRelationsChangeSetEntries = self::getMediaManyToOneRelationsChangeSetEntries(
            $entityNew,
            $entityOld
        );
        $mediaOneToManyRelationsChangeSetEntries = self::getMediaOneToManyRelationsChangeSetEntries(
            $entityNew,
            $entityOld
        );
        $translatableFieldsChangeSetEntries = self::getTranslatableFieldsChangeSetEntries(
            $entityNew,
            $entityOld
        );
        $seoFieldsChangeSetEntries = self::getSeoFieldsChangeSetEntries(
            $entityNew,
            $entityOld
        );

        $changeSetEntries = array_merge(
            $commonFieldsChangeSetEntries,
            $manyToOneRelationsChangeSetEntries,
            $oneToManyRelationsChangeSetEntries,
            $manyToManyRelationsChangeSetEntries,
            $mediaManyToOneRelationsChangeSetEntries,
            $mediaOneToManyRelationsChangeSetEntries,
            $translatableFieldsChangeSetEntries,
            $seoFieldsChangeSetEntries
        );

        $changeSet = new ChangeSet();

        /** @var ChangeSetEntry $entry */
        foreach ($changeSetEntries as $entry) {
            $entry->setChangeSet($changeSet);
            $changeSet->addEntry($entry);

            $em->persist($entry);
        }

        $em->persist($changeSet);

        return $changeSet;
    }

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

    private static function serializeEntityValue($entity)
    {
        $data = [
            'id'    => $entity->getId(),
            'value' => (string)$entity,
        ];

        return json_encode($data);
    }

    private static function serializeEntitiesCollectionValue($entities)
    {
        $data = [];

        foreach ($entities as $entity) {
            $data[] = [
                'id'    => $entity->getId(),
                'value' => (string)$entity,
            ];
        }

        return json_encode($data);
    }

    /**
     * @param VideoMedia $video
     * @return string
     */
    public static function serializeBusinessProfileVideo(VideoMedia $video)
    {
        $businessVideoRequiredData = [
            'id'          => $video->getId(),
            'name'        => $video->getName() ?: null,
            'title'       => $video->getTitle() ?: null,
            'description' => $video->getDescription() ?: null,
        ];

        return json_encode($businessVideoRequiredData);
    }

    public static function serializeBusinessProfileMediaItem(Media $item)
    {
        $businessMediaRequiredData = [
            'id'   => $item->getId(),
            'name' => $item->getName(),
        ];

        return json_encode($businessMediaRequiredData);
    }

    /**
     * @param BusinessGallery[] $collection
     * @return string
     */
    public static function serializeBusinessGalleryCollection($collection)
    {
        $businessGalleryRequiredData = [];

        foreach ($collection as $businessGallery) {
            $businessGalleryRequiredData[] = [
                'id'          => $businessGallery->getId(),
                'media'       => $businessGallery->getMedia()->getId(),
                'description' => $businessGallery->getDescription(),
            ];
        }

        return json_encode($businessGalleryRequiredData);
    }

    /**
     * @param BusinessGallery $gallery
     * @return string
     */
    public static function serializeBusinessGalleryItem($gallery)
    {
        $businessGalleryData[] = [
            'media'       => $gallery->getMedia()->getId(),
            'description' => $gallery->getDescription() ?: null,
        ];

        return json_encode($businessGalleryData);
    }

    public static function getCommonFieldsChangeSetEntries($entityNew, $entityOld)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskCommonFields() as $field) {
            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            if ($valueNew != $valueOld) {
                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    $valueOld,
                    $valueNew,
                    self::CHANGE_COMMON_PROPERTY
                );
            }
        }

        return $changeSetEntries;
    }

    public static function getManyToOneRelationsChangeSetEntries($entityNew, $entityOld, EntityManagerInterface $em)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskManyToOneRelations() as $field) {
            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            if ($valueNew->getId() != $valueOld->getId()) {
                $class = $em->getClassMetadata(get_class($valueNew))->name;

                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    self::serializeEntityValue($valueOld),
                    self::serializeEntityValue($valueNew),
                    self::CHANGE_RELATION_MANY_TO_ONE,
                    $class
                );
            }
        }

        return $changeSetEntries;
    }

    public static function getOneToManyRelationsChangeSetEntries($entityNew, $entityOld, EntityManagerInterface $em)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskOneToManyRelations() as $field) {
            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            $updated = false;

            if ($valueNew->count() == $valueOld->count()) {
                foreach ($valueNew as $key => $item) {
                    if ((string)$item != (string)$valueOld[$key]) {
                        $updated = true;
                        break;
                    }
                }
            } else {
                $updated = true;
            }

            if ($updated) {
                if ($valueNew and !$valueNew->isEmpty()) {
                    $class = $em->getClassMetadata(get_class($valueNew->current()))->name;
                } else {
                    $class = $em->getClassMetadata(get_class($valueOld->current()))->name;
                }

                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    self::serializeEntitiesCollectionValue($valueOld),
                    self::serializeEntitiesCollectionValue($valueNew),
                    self::CHANGE_RELATION_ONE_TO_MANY,
                    $class
                );
            }
        }

        return $changeSetEntries;
    }

    public static function getManyToManyRelationsChangeSetEntries($entityNew, $entityOld, EntityManagerInterface $em)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskManyToManyRelations() as $field) {
            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            if (!$valueOld) {
                $valueOld = [];
            }

            if (!$valueNew) {
                $valueNew = [];
            }

            $updated = false;
            $class   = '';

            if ($field == BusinessProfile::BUSINESS_PROFILE_RELATION_LOCALITIES or
                $field == BusinessProfile::BUSINESS_PROFILE_RELATION_NEIGHBORHOODS
            ) {
                if ($valueNew and $valueOld and count($valueNew) == $valueOld->count()) {
                    $newIds = [];
                    $oldIds = [];

                    foreach ($valueNew as $key => $item) {
                        $newIds[] = $item->getId();
                    }

                    foreach ($valueOld as $key => $item) {
                        $oldIds[] = $item->getId();
                    }

                    asort($newIds);
                    asort($oldIds);

                    if ($newIds != $oldIds) {
                        $updated = false;
                    }

                } elseif ($valueOld and $valueOld->isEmpty() and !$valueNew) {
                    $updated = false;
                } else {
                    $updated = true;
                }

                if ($updated) {
                    $class = self::getClassByRelationConst($field);
                }
            } else {
                if ($valueNew and $valueOld and $valueNew->count() == $valueOld->count()) {
                    $newIds = [];
                    $oldIds = [];

                    foreach ($valueNew as $key => $item) {
                        $newIds[] = $item->getId();
                    }

                    foreach ($valueOld as $key => $item) {
                        $oldIds[] = $item->getId();
                    }

                    asort($newIds);
                    asort($oldIds);

                    if ($newIds != $oldIds) {
                        $updated = false;
                    }
                } else {
                    $updated = true;
                }

                if ($updated) {
                    if ($valueNew and !$valueNew->isEmpty()) {
                        $class = $em->getClassMetadata(get_class($valueNew->current()))->name;
                    } else {
                        $class = $em->getClassMetadata(get_class($valueOld->current()))->name;
                    }
                }
            }

            if ($updated) {
                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    self::serializeEntitiesCollectionValue($valueOld),
                    self::serializeEntitiesCollectionValue($valueNew),
                    self::CHANGE_RELATION_MANY_TO_MANY,
                    $class
                );
            }
        }

        return $changeSetEntries;
    }

    public static function getMediaManyToOneRelationsChangeSetEntries($entityNew, $entityOld)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskMediaManyToOneRelations() as $field) {
            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            $itemNew = null;
            $itemOld = null;

            if ($field == BusinessProfile::BUSINESS_PROFILE_RELATION_VIDEO) {
                $class = VideoMedia::class;

                if ($valueNew) {
                    $itemNew = self::serializeBusinessProfileVideo($valueNew);
                }

                if ($valueOld) {
                    $itemOld = self::serializeBusinessProfileVideo($valueOld);
                }
            } else {
                $class = Media::class;

                if ($valueNew) {
                    $itemNew = self::serializeBusinessProfileMediaItem($valueNew);
                }

                if ($valueOld) {
                    $itemOld = self::serializeBusinessProfileMediaItem($valueOld);
                }
            }

            if ($itemNew != $itemOld) {
                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    $itemOld,
                    $itemNew,
                    self::CHANGE_MEDIA_RELATION_MANY_TO_ONE,
                    $class
                );
            }
        }

        return $changeSetEntries;
    }

    public static function getMediaOneToManyRelationsChangeSetEntries($entityNew, $entityOld)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskMediaOneToManyRelations() as $field) {
            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            if ($field == BusinessProfile::BUSINESS_PROFILE_RELATION_IMAGES) {
                $class = BusinessGallery::class;

                $updated = false;

                if ($valueNew and $valueOld and $valueNew->count() == $valueOld->count()) {
                    foreach ($valueNew as $key => $itemNew) {
                        $itemNewCompare = self::serializeBusinessGalleryItem($itemNew);
                        $itemOldCompare = self::serializeBusinessGalleryItem($valueOld[$key]);

                        if ($itemNewCompare != $itemOldCompare) {
                            $updated = true;
                            break;
                        }
                    }
                } else {
                    $updated = true;
                }

                if ($updated) {
                    $itemNew = null;
                    $itemOld = null;

                    if (!$valueNew->isEmpty()) {
                        $itemNew = self::serializeBusinessGalleryCollection($valueNew);
                    }

                    if (!$valueOld->isEmpty()) {
                        $itemOld = self::serializeBusinessGalleryCollection($valueOld);
                    }

                    if ($itemNew != $itemOld) {
                        $changeSetEntries[] = self::buildChangeSetEntryObject(
                            $field,
                            $itemOld,
                            $itemNew,
                            self::CHANGE_MEDIA_RELATION_ONE_TO_MANY,
                            $class
                        );
                    }
                }
            }
        }

        return $changeSetEntries;
    }

    public static function getTranslatableFieldsChangeSetEntries($entityNew, $entityOld)
    {
        $changeSetEntries = [];

        foreach (BusinessProfile::getTranslatableFields() as $field) {
            $data = self::getTranslatableLocaleChangeSetEntries($entityNew, $entityOld, $field);
            $changeSetEntries = array_merge($changeSetEntries, $data);
        }

        return $changeSetEntries;
    }

    public static function getSeoFieldsChangeSetEntries($entityNew, $entityOld)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $changeSetEntries = [];

        foreach (BusinessProfile::getTaskSeoBlock() as $field) {
            $data = self::getTranslatableLocaleChangeSetEntries($entityNew, $entityOld, $field);
            $changeSetEntries = array_merge($changeSetEntries, $data);

            $valueNew = $accessor->getValue($entityNew, $field);
            $valueOld = $accessor->getValue($entityOld, $field);

            if ($valueNew != $valueOld) {
                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    $valueOld,
                    $valueNew,
                    self::CHANGE_COMMON_PROPERTY
                );
            }
        }

        return $changeSetEntries;
    }

    public static function getLocales()
    {
        return [
            strtolower(BusinessProfile::TRANSLATION_LANG_EN),
            strtolower(BusinessProfile::TRANSLATION_LANG_ES),
        ];
    }

    public static function getTranslatableLocaleChangeSetEntries($entityNew, $entityOld, $field)
    {
        $changeSetEntries = [];

        foreach (self::getLocales() as $locale) {
            $valueNew = (string)$entityNew->getTranslationItem($field, $locale);
            $valueOld = (string)$entityOld->getTranslationItem($field, $locale);

            if ($valueNew != $valueOld) {
                $changeSetEntries[] = self::buildChangeSetEntryObject(
                    $field,
                    $valueOld,
                    $valueNew,
                    self::CHANGE_TRANSLATION,
                    BusinessProfileTranslation::class
                );
            }
        }

        return $changeSetEntries;
    }

    /**
     * @param string $relation
     *
     * @return string
     */
    public static function getClassByRelationConst($relation)
    {
        switch ($relation) {
            case BusinessProfile::BUSINESS_PROFILE_RELATION_LOCALITIES:
                $class = Locality::class;
                break;
            case BusinessProfile::BUSINESS_PROFILE_RELATION_NEIGHBORHOODS:
                $class = Neighborhood::class;
                break;
            default:
                $class = '';
                break;
        }

        return $class;
    }
}
