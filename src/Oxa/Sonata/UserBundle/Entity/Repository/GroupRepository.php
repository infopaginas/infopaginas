<?php
declare(strict_types=1);

namespace Oxa\Sonata\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class GroupRepository extends EntityRepository
{
    /**
     * @param int $code
     * @return array
     */
    public function getEqualOrLowerPriorityRoles(int $code)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->from('OxaSonataUserBundle:Group', 'g', 'g.id')
            ->select('g')
            ->andWhere('g.code >= :code')
            ->setParameter(':code', $code)
            ->orderBy('g.code')
            ->getQuery()
            ->getResult()
        ;
    }

}
