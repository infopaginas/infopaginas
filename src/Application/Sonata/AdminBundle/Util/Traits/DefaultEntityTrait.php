<?php

namespace Application\Sonata\AdminBundle\Util\Traits;

trait DefaultEntityTrait
{
	use DeleteableUserEntityTrait, TimestampableUserEntityTrait, AvailableUserEntityTrait;
}