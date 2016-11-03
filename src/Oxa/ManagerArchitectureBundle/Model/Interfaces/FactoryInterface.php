<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

interface FactoryInterface extends ServiceInterface
{
    /**
     * Main factory function which accepts $type param
     * for future building of some future object
     *
     * @param mixed $type
     * @return mixed
     */
    public function get($type);
}
