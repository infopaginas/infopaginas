<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

/**
 * Class ChangeStateTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait ChangeStateTrait
{
    protected $changeState;

    public function getChangeState()
    {
        return $this->changeState;
    }

    public function setChangeState(array $changeState) : self
    {
        $this->changeState = $changeState;

        return $this;
    }
}
