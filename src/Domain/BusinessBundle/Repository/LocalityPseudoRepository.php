<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Domain\BusinessBundle\Entity\LocalityPseudo;

class LocalityPseudoRepository extends EntityRepository
{
    /**
     * @param string $localitySlug
     *
     * @return LocalityPseudo|null
     */
    public function getLocalityPseudoBySlug($localitySlug)
    {
        $query = $this->createQueryBuilder('lp')
            ->where('lp.slug = :localitySlug')
            ->setParameter('localitySlug', $localitySlug)
        ;

        $query->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }
}
