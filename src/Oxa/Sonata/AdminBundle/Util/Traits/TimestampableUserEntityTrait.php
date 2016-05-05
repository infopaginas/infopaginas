<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;


use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Extend TimestampableEntity trait with set user functionality
 *
 * Class TimestampableUserEntityTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait TimestampableUserEntityTrait
{
    use TimestampableEntity, UserCUableEntityTrait;
}
