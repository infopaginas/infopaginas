<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

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
