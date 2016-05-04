<?php

namespace Application\Sonata\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{
    /**
     * Delete record from database
     *
     * @param object $entity
     */
    public function deletePhysicalEntity(object $entity)
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(get_class($entity), 'e')
            ->where('e.id=:id')
            ->setParameter(':id', $entity->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * Restore deleted
     *
     * @param $entityClass
     * @param $id
     */
    public function restoreEntity(string $entityClass, int $id)
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->update($entityClass, 'e')
            ->set('e.deletedUser', 'NULL')
            ->set('e.deletedAt', 'NULL')
            ->where('e.id=:id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->execute();
    }
}
