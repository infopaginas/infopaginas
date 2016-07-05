<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/16/16
 * Time: 8:13 PM
 */

namespace Domain\BusinessBundle\Model;
use Oxa\Sonata\AdminBundle\Model\DatetimePeriodInterface;

/**
 * Class DatetimePeriodStatusInterface
 * @package Domain\BusinessBundle\Model
 */
interface DatetimePeriodStatusInterface extends DatetimePeriodInterface, StatusInterface, BusinessProfileRelationInterface
{
}
