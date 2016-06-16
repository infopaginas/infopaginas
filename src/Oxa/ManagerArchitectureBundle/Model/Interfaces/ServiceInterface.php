<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

use Doctrine\ORM\EntityManager;

interface ServiceInterface
{
    /**
     * Manager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager);
}
