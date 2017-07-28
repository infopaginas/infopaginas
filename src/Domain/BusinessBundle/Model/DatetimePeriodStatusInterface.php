<?php

namespace Domain\BusinessBundle\Model;

use Oxa\Sonata\AdminBundle\Model\DatetimePeriodInterface;

/**
 * Class DatetimePeriodStatusInterface
 * @package Domain\BusinessBundle\Model
 */
interface DatetimePeriodStatusInterface extends
    DatetimePeriodInterface,
    StatusInterface,
    BusinessProfileRelationInterface
{
}
