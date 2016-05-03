<?php

namespace Application\Sonata\AdminBundle\Util\Traits;


use Gedmo\Timestampable\Traits\TimestampableEntity;

trait TimestampableUserEntityTrait
{
	use TimestampableEntity, UserCUableEntityTrait;
}