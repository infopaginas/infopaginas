<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 30.08.16
 * Time: 14:05
 */

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
