<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

/**
 * Interface DataTransferObjectInterface
 * @package Oxa\ManagerArchitectureBundle\Model\Interfaces
 */
interface DataTransferObjectInterface
{
    public function serialize() : string;

    public static function deserialize(string $serialized);
}
