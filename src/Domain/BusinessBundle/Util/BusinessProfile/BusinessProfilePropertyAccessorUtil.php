<?php

namespace Domain\BusinessBundle\Util\BusinessProfile;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * Class BusinessProfilePropertyAccessorUtil
 * @package Domain\BusinessBundle\Util\BusinessProfile
 */
class BusinessProfilePropertyAccessorUtil
{
    /**
     * @param $entity
     * @param EntityManagerInterface $em
     * @return array
     */
    public static function getBusinessProfilePropertiesHavingCollectionType(EntityManagerInterface $em, $entity)
    {
        $class = get_class($entity);

        $propertyInfoExtractor = self::getPropertyInfoExtractor($em);

        $properties = $propertyInfoExtractor->getProperties($class);

        $entityCollections = [];

        foreach ($properties as $property) {
            $types = $propertyInfoExtractor->getTypes($class, $property);
            if (is_array($types) && isset($types[0])) {
                if ($types[0]->getClassName() == Collection::class) {
                    $entityCollections[$types[0]->getCollectionValueType()->getClassName()] = $property;
                }
            }
        }

        $entityCollections[\Domain\BusinessBundle\Entity\Category::class] = 'categories';

        return $entityCollections;
    }

    /**
     * @param $entity
     * @param $property
     * @param $insertDiff
     * @param $deleteDiff
     * @return mixed
     */
    public static function getOriginalBusinessProfileCollectionValues($entity, $property, $insertDiff, $deleteDiff)
    {
        $collection = self::getBusinessProfilePropertyValue($entity, $property);

        foreach ($insertDiff as $item) {
            $collection->removeElement($item);
        }

        foreach ($deleteDiff as $item) {
            $collection->add($item);
        }

        return $collection;
    }

    /**
     * @param $entity
     * @param string $property
     * @return mixed
     */
    public static function getBusinessProfilePropertyValue($entity, string $property)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        return $accessor->getValue($entity, $property);
    }

    /**
     * @param EntityManagerInterface $em
     * @return PropertyInfoExtractor
     */
    private static function getPropertyInfoExtractor(EntityManagerInterface $em) : PropertyInfoExtractor
    {
        $reflectionExtractor = new ReflectionExtractor();
        $doctrineExtractor = new DoctrineExtractor($em->getMetadataFactory());

        $listExtractors = $typeExtractors = [$reflectionExtractor, $doctrineExtractor];

        return new PropertyInfoExtractor($listExtractors, $typeExtractors);
    }
}
