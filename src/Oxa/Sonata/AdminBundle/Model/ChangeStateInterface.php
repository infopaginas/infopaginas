<?php

namespace Oxa\Sonata\AdminBundle\Model;

/**
 * Used for holding data before on pre update entity
 *
 * Interface ChangeStateInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface ChangeStateInterface
{
    public function setChangeState(array $changeState);

    public function getChangeState();
}
