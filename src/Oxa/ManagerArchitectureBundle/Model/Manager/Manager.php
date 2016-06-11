<?php

namespace Oxa\ManagerArchitectureBundle\Model\Manager;

abstract class Manager
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
