<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 30.08.16
 * Time: 14:05
 */

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
