<?php
namespace Oxa\ManagerArchitectureBundle\Model\Factory;

use Doctrine\ORM\EntityManager;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FactoryInterface;

abstract class Factory implements FactoryInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * MenuManager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
}
