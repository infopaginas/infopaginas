<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/14/16
 * Time: 12:02 PM
 */
declare(strict_types=1);

namespace Oxa\Sonata\AdminBundle\Manager;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DeleteableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;

/**
 * Used to customise admin
 *
 * Class AdminManager
 * @package Oxa\Sonata\AdminBundle\Manager
 */
class AdminManager extends DefaultManager
{
    /**
     * Get object even from deleted(soft) records if $disableSoftdelete param equals True
     *
     * @param string $entityClass
     * @param int $id
     * @param bool $disableSoftdelete
     * @return mixed
     * @throws InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getObjectByClassName(string $entityClass, int $id, $disableSoftdelete = false)
    {
        if ($disableSoftdelete) {
            $this->disableDeleteableListener($entityClass);
        }

        $this->checkIfEntityClassIsValid($entityClass);

        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e')
            ->where('e.id=:id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Delete record completely
     *
     * @param $entity
     * @throws InvalidArgumentException
     */
    public function deletePhysicalEntity(DeleteableEntityInterface $entity)
    {
        // execute query here (not in repository),
        // cuz a repository requires to be related on mapped entity
        // but here entity is not specified
        $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(get_class($entity), 'e')
            ->where('e.id=:id')
            ->setParameter(':id', $entity->getId())
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Restore deleted(soft) record
     *
     * @param $entity
     * @throws InvalidArgumentException
     */
    public function restoreEntity(DeleteableEntityInterface $entity)
    {
        // execute query here (not in repository),
        // be course a repository requires to be related on mapped entity
        // but here entity is not specified
        $this->getEntityManager()
            ->createQueryBuilder()
            ->update(get_class($entity), 'e')
            ->set('e.' . DeleteableEntityInterface::DELETED_USER_PROPERTY_NAME, 'NULL')
            ->set('e.' . DeleteableEntityInterface::DELETED_AT_PROPERTY_NAME, 'NULL')
            ->where('e.id=:id')
            ->setParameter(':id', $entity->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * Restore deleted(soft) record
     *
     * @param string $entityClass
     * @param int $id
     * @param bool $disableSoftdelete
     */
    public function restoreEntityByClassName(string $entityClass, int $id, $disableSoftdelete = false)
    {
        $object = $this->getObjectByClassName($entityClass, $id, $disableSoftdelete);
        $this->restoreEntity($object);
    }

    /**
     * Clone and persist object
     *
     * @param CopyableEntityInterface $entity
     * @return CopyableEntityInterface
     * @throws \Throwable
     * @throws \TypeError
     */
    protected function cloneEntityObject(CopyableEntityInterface $entity)
    {
        $propertyAccessor = $this->getContainer()->get('property_accessor');
        $copyMark = $this->getContainer()->get('translator')->trans('copy_', [], 'SonataAdminBundle');

        $clone = clone $entity;
        $value = $propertyAccessor->getValue($clone, $entity->getMarkCopyPropertyName());
        $propertyAccessor->setValue($clone, $entity->getMarkCopyPropertyName(), sprintf('%s%s', $copyMark, $value));

        $this->getEntityManager()->persist($clone);

        return $clone;
    }

    /**
     * Clone object with all relations
     *
     * @param CopyableEntityInterface $entity
     * @return CopyableEntityInterface
     * @throws \Throwable
     * @throws \TypeError
     */
    public function cloneEntity(CopyableEntityInterface $entity)
    {
        $this->cloneEntityObject($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Disable Deleteable Listener to work with deleted object as well
     *
     * @param $entityClass
     */
    public function disableDeleteableListener(string $entityClass)
    {
        $this->checkIfEntityClassIsValid($entityClass);

        /**
         * @var SoftDeleteableFilter $softDeleteableFilter
         */
        $softDeleteableFilter = $this->getEntityManager()
            ->getFilters()
            ->getFilter('softdeleteable');

        $softDeleteableFilter->disableForEntity($entityClass);
    }

    /**
     * Check if entity has relation with other entities
     *
     * @param $entity
     * @return array
     */
    public function checkExistDependentEntity($entity)
    {
        $metadata = $this->getEntityManager()->getClassMetadata(get_class($entity));
        $existDependentField = [];
        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            if (
                $associationMapping['type'] == ClassMetadataInfo::ONE_TO_MANY ||
                $associationMapping['type'] == ClassMetadataInfo::ONE_TO_ONE
            ) {
                $methodGet = 'get' . ucfirst($associationMapping['fieldName']);
                $childs = $entity->$methodGet();
                if (count($childs)) {
                    $existDependentField[] = $this->getContainer()->get('translator')->trans(
                        'form.label_' . $associationMapping['fieldName'],
                        []
                    );
                }
            }
        }
        return $existDependentField;
    }

    /**
     * Check if such entity class really exists
     *
     * @param $entityClass
     * @throws InvalidArgumentException
     */
    protected function checkIfEntityClassIsValid(string $entityClass)
    {
        $mappedEntities = $this->getEntityManager()
            ->getConfiguration()
            ->getMetadataDriverImpl()
            ->getAllClassNames();

        if (!in_array($entityClass, $mappedEntities)) {
            throw new InvalidArgumentException(sprintf('Entity "%s" does not exist', $entityClass));
        }
    }

    /**
     * Delete records softly
     *
     * @param array $entityArray
     */
    public function removeEntities(array $entityArray = [])
    {
        foreach ($entityArray as $entity) {
            if ($entity instanceof DeleteableEntityInterface && is_null($entity->getDeletedAt())) {
                $this->getEntityManager()->remove($entity);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Delete records completely
     *
     * @param array $entityArray
     */
    public function physicalDeleteEntities(array $entityArray = [], $disableSoftdelete = false)
    {
        foreach ($entityArray as $entity) {
            if ($disableSoftdelete) {
                $this->disableDeleteableListener(get_class($entity));
            }

            if ($entity instanceof DeleteableEntityInterface) {
                $this->deletePhysicalEntity($entity);
            }
        }
    }

    /**
     * Restore deleted(soft) records
     *
     * @param array $entityArray
     */
    public function restoreEntities(array $entityArray = [], $disableSoftdelete = false)
    {
        foreach ($entityArray as $entity) {
            if ($disableSoftdelete) {
                $this->disableDeleteableListener(get_class($entity));
            }

            if ($entity instanceof DeleteableEntityInterface && !is_null($entity->getDeletedAt())) {
                $this->restoreEntity($entity);
            }
        }
    }

    /**
     * Clone objects with all relations
     *
     * @param array $entityArray
     */
    public function cloneEntities(array $entityArray = [])
    {
        foreach ($entityArray as $entity) {
            if ($entity instanceof CopyableEntityInterface) {
                $this->cloneEntityObject($entity);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Used in twig extension
     *
     * @param $entityClass
     * @param array $entityIdList
     * @return array
     */
    public function getObjectList($entityClass, array $entityIdList)
    {
        $qb = $this->getEntityManager()
            ->getRepository($entityClass)
            ->createQueryBuilder('o');

        return $qb
            ->where(
                $qb->expr()->in('o.id', $entityIdList)
            )
            ->getQuery()
            ->getResult();
    }
}
