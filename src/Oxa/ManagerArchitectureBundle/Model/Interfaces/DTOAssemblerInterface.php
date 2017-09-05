<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DTOAssemblerInterface
 * @package Oxa\ManagerArchitectureBundle\Model\Interfaces
 */
interface DTOAssemblerInterface
{
    public function createDTO($input) : DataTransferObjectInterface;

    public function createDO(string $serialized) : ArrayCollection;
}
