<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

interface MagicAccessInterface
{
    public function __get($property);
    public function __set($property, $value);
}
