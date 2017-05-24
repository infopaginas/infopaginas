<?php

namespace Domain\BusinessBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * NeighborhoodRepository
 */
class NeighborhoodRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAvailableNeighborhoodsQb()
    {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.name');

        return $qb;
    }

    /**
     * @param array  $localities
     * @param string $locale
     *
     * @return Neighborhood[]
     */
    public function getAvailableNeighborhoodsByLocalities($localities, $locale)
    {
        $qb = $this->getAvailableNeighborhoodsQb()
            ->andWhere('n.locality IN (:localities)')
            ->setParameter('localities', $localities)
        ;

        $query = $qb->getQuery();

        if ($locale) {
            $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            );

            // Force the locale
            $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                $locale
            );
        }

        return $query->getResult();
    }
}
