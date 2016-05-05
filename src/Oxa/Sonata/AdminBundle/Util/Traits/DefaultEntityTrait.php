<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

/**
 * Should be added to all entities for extended CRUD functional
 *
 * Class DefaultEntityTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait DefaultEntityTrait
{
    use DeleteableUserEntityTrait, TimestampableUserEntityTrait, AvailableUserEntityTrait;
}
