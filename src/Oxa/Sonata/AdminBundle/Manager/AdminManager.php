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
 * Class AdminManager
 * @package Oxa\Sonata\AdminBundle\Manager
 */
class AdminManager extends DefaultManager
{
    /**
     * @param string $entityClass
     * @param int $id
     * @param bool $disableSoftdelete
     * @return mixed
     * @throws InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getObjectByClassName(string $entityClass, int $id, $disableSoftdelete = false)
    {
        if ($disableSoftdelete)
            $this->disableDeleteableListener($entityClass);

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
     * @param $entity
     * @throws InvalidArgumentException
     */
    public function deletePhysicalEntity(DeleteableEntityInterface $entity)
    {
        // execute query here (not in repository),
        // be course a repository requires to be related on mapped entity
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
     * @param $entity
     * @throws InvalidArgumentException
     */
    public function remove(DeleteableEntityInterface $entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @param $entity
     */
    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     *
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @param $entity
     * @throws InvalidArgumentException
     */
    public function restoreEntity(DeleteableEntityInterface $entity)
    {
        // execute query here (not in repository),
        // be course a repository requires to be related on mapped entity
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
     * @param CopyableEntityInterface $entity
     * @return CopyableEntityInterface
     * @throws \Throwable
     * @throws \TypeError
     */
    public function cloneEntity(CopyableEntityInterface $entity)
    {
        $propertyAccessor = $this->getContainer()->get('property_accessor');
        $copyMark = $this->getContainer()->get('translator')->trans('copy_', [], 'SonataAdminBundle');

        $clone = clone $entity;
        $value = $propertyAccessor->getValue($clone, $entity->getMarkCopyPropertyName());
        $propertyAccessor->setValue($clone, $entity->getMarkCopyPropertyName(), sprintf('%s%s', $copyMark, $value));

        $this->getEntityManager()->persist($clone);
//        $this->getEntityManager()->flush();

        return $clone;
    }

    /**
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
     * @param $entity
     * @return array
     */
    public function checkExistDependentEntity($entity)
    {
        $metadata = $this->getEntityManager()->getClassMetadata(get_class($entity));
        $existDependentField = [];
        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            if ($associationMapping['type'] == ClassMetadataInfo::ONE_TO_MANY) {
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
     * @param $entityClass
     * @throws InvalidArgumentException
     */
    protected function checkIfEntityClassIsValid(string $entityClass)
    {
        $mappedEntities = $this->getEntityManager()
            ->getConfiguration()
            ->getMetadataDriverImpl()
            ->getAllClassNames();

        if (!in_array($entityClass, $mappedEntities))
            throw new InvalidArgumentException(sprintf('Entity "%s" does not exist', $entityClass));
    }

    /**
     * @param array $entityArray
     */
    public function removeEntities(array $entityArray = [])
    {
        foreach ($entityArray as $entity) {
            if ($entity instanceof DeleteableEntityInterface && is_null($entity->getDeletedAt()))
                $this->getEntityManager()->remove($entity);
        }
    }

    /**
     * @param array $entityArray
     */
    public function physicalDeleteEntities(array $entityArray = [])
    {
        foreach ($entityArray as $entity) {
            if ($entity instanceof DeleteableEntityInterface)
                $this->deletePhysicalEntity($entity);
        }
    }

    /**
     * @param array $entityArray
     */
    public function restoreEntities(array $entityArray = [])
    {
        // TODO Flash one query
        foreach ($entityArray as $entity) {
            if ($entity instanceof DeleteableEntityInterface && !is_null($entity->getDeletedAt())) {
                $this->restoreEntity($entity);
            }

        }
    }

    /**
     * @param array $entityArray
     */
    public function cloneEntities(array $entityArray = [])
    {
        foreach ($entityArray as $entity) {
            if ($entity instanceof CopyableEntityInterface)
                $this->cloneEntity($entity);
        }
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