<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 30.08.16
 * Time: 15:05
 */

namespace Domain\BusinessBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;

/**
 * Class DoctrineUtil
 * @package Domain\BusinessBundle\Util
 */
class DoctrineUtil
{
    /**
     * Try to get an Entity changeSet without changing the UnitOfWork
     *
     * @param EntityManagerInterface $em
     * @param $entity
     * @return null|array
     */
    public static function diffDoctrineObject(EntityManagerInterface $em, $entity)
    {
        $uow = $em->getUnitOfWork();

        /*****************************************/
        /* Equivalent of $uow->computeChangeSet($this->em->getClassMetadata(get_class($entity)), $entity);
        /*****************************************/
        $class = $em->getClassMetadata(get_class($entity));
        $oid = spl_object_hash($entity);
        $entityChangeSets = array();

        if ($uow->isReadOnly($entity)) {
            return null;
        }

        if (!$class->isInheritanceTypeNone()) {
            $class = $em->getClassMetadata(get_class($entity));
        }

        $actualData = array();

        foreach ($class->reflFields as $name => $refProp) {
            $value = $refProp->getValue($entity);

            if ($class->isCollectionValuedAssociation($name) && $value !== null) {
                if ($value instanceof PersistentCollection) {
                    if ($value->getOwner() === $entity) {
                        continue;
                    }

                    $value = new ArrayCollection($value->getValues());
                }

                // If $value is not a Collection then use an ArrayCollection.
                if (!$value instanceof Collection) {
                    $value = new ArrayCollection($value);
                }

                $assoc = $class->associationMappings[$name];

                // Inject PersistentCollection
                $value = new PersistentCollection($em, $em->getClassMetadata($assoc['targetEntity']), $value);
                $value->setOwner($entity, $assoc);
                $value->setDirty(!$value->isEmpty());

                $class->reflFields[$name]->setValue($entity, $value);

                $actualData[$name] = $value;

                continue;
            }

            if ((!$class->isIdentifier($name) or !$class->isIdGeneratorIdentity()) and
                ($name !== $class->versionField)
            ) {
                $actualData[$name] = $value;
            }
        }

        $originalEntityData = $uow->getOriginalEntityData($entity);
        if (empty($originalEntityData)) {
            // Entity is either NEW or MANAGED but not yet fully persisted (only has an id).
            // These result in an INSERT.
            $originalEntityData = $actualData;
            $changeSet = array();

            foreach ($actualData as $propName => $actualValue) {
                if (!isset($class->associationMappings[$propName])) {
                    $changeSet[$propName] = array(null, $actualValue);

                    continue;
                }

                $assoc = $class->associationMappings[$propName];

                if ($assoc['isOwningSide'] && $assoc['type'] & ClassMetadata::TO_ONE) {
                    $changeSet[$propName] = array(null, $actualValue);
                }
            }

            $entityChangeSets[$oid] = $changeSet; // @todo - remove this?
        } else {
            // Entity is "fully" MANAGED: it was already fully persisted before
            // and we have a copy of the original data
            $originalData           = $originalEntityData;
            $isChangeTrackingNotify = $class->isChangeTrackingNotify();
            $changeSet              = $isChangeTrackingNotify ? $uow->getEntityChangeSet($entity) : array();

            foreach ($actualData as $propName => $actualValue) {
                // skip field, its a partially omitted one!
                if (!(isset($originalData[$propName]) || array_key_exists($propName, $originalData))) {
                    continue;
                }

                $orgValue = $originalData[$propName];

                // skip if value haven't changed
                if ($orgValue === $actualValue) {
                    continue;
                }

                // if regular field
                if (!isset($class->associationMappings[$propName])) {
                    if ($isChangeTrackingNotify) {
                        continue;
                    }

                    $changeSet[$propName] = array($orgValue, $actualValue);

                    continue;
                }

                $assoc = $class->associationMappings[$propName];

                // Persistent collection was exchanged with the "originally"
                // created one. This can only mean it was cloned and replaced
                // on another entity.
                if ($actualValue instanceof PersistentCollection) {
                    $owner = $actualValue->getOwner();
                    if ($owner === null) { // cloned
                        $actualValue->setOwner($entity, $assoc);
                    } elseif ($owner !== $entity) { // no clone, we have to fix
                        // @todo - what does this do... can it be removed?
                        if (!$actualValue->isInitialized()) {
                            $actualValue->initialize(); // we have to do this otherwise the cols share state
                        }
                        $newValue = clone $actualValue;
                        $newValue->setOwner($entity, $assoc);
                        $class->reflFields[$propName]->setValue($entity, $newValue);
                    }
                }

                if ($orgValue instanceof PersistentCollection) {
                    $changeSet[$propName] = $orgValue; // Signal changeset, to-many assocs will be ignored.

                    continue;
                }

                if ($assoc['type'] & ClassMetadata::TO_ONE) {
                    if ($assoc['isOwningSide']) {
                        $changeSet[$propName] = array($orgValue, $actualValue);
                    }
                }
            }

            $entityChangeSets[$oid]     = $changeSet;
        }

        return $entityChangeSets[$oid];
    }
}
