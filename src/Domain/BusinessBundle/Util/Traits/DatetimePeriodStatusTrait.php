<?php

namespace Domain\BusinessBundle\Util\Traits;

use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Exception\InvalidArgumentException;
use Oxa\Sonata\AdminBundle\Util\Traits\DatetimePeriodTrait;

/**
 * Class DatetimePeriodStatusTrait
 * @package Domain\BusinessBundle\Util\Traits
 */
trait DatetimePeriodStatusTrait
{
    use DatetimePeriodTrait;
    use StatusTrait;
}
