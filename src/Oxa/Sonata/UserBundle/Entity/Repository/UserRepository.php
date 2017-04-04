<?php
declare(strict_types=1);

namespace Oxa\Sonata\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{
    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->leftJoin('u.groups', 'g')
            ->where($qb->expr()->orX(
                $qb->expr()->like('u.roles', ':roles'),
                $qb->expr()->like('g.roles', ':roles')
            ))
            ->setParameter('roles', '%"' . $role . '"%');

        return $qb->getQuery()->getResult();
    }

    public function getManagedBusinessesData()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(bp.id) as cnt, u.id as userId')
            ->leftJoin('u.businessProfiles', 'bp')
            ->groupBy('u.id')
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
