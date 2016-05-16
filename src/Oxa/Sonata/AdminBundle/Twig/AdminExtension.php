<?php

namespace Oxa\Sonata\AdminBundle\Twig;

use Doctrine\ORM\EntityManager;

/**
 * Create new twig functions
 * 
 * Class AdminExtension
 * @package Oxa\Sonata\AdminBundle\Twig
 */
class AdminExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * AdminExtension constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'admin_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'get_object_list' => new \Twig_SimpleFunction('get_object_list', [$this, 'getObjectList'])
        ];
    }

    /**
     * Get object list by ids
     *
     * @param $entityClass
     * @param array $idList
     * @return array
     */
    public function getObjectList($entityClass, array $idList)
    {
        $qb = $this->entityManager
            ->getRepository($entityClass)
            ->createQueryBuilder('o');

        return $qb
            ->where(
                $qb->expr()->in('o.id', $idList)
            )
            ->getQuery()
            ->getResult();
    }
}
