<?php

namespace Application\Sonata\AdminBundle\Util\Traits;

/**
 * Should be added to all entities for extended CRUD functional
 * 
 * Class DefaultEntityTrait
 * @package Application\Sonata\AdminBundle\Util\Traits
 */
trait DefaultEntityTrait
{
    use DeleteableUserEntityTrait, TimestampableUserEntityTrait, AvailableUserEntityTrait;
}
