<?php

namespace Oxa\ManagerArchitectureBundle\Model\DataType;

class AbstractValueObject
{
    public function __get($property)
    {
        throw new RuntimeException('Trying to get non-existing property ' . $property);
    }

    public function __set($property, $value)
    {
        throw new RuntimeException('Trying to set non-existing property ' . $property);
    }
}
