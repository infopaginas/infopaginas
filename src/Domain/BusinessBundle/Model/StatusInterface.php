<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/16/16
 * Time: 8:13 PM
 */

namespace Domain\BusinessBundle\Model;

/**
 * Class SubscriptionInterface
 * @package Domain\BusinessBundle\Model
 */
interface StatusInterface
{
    const STATUS_ACTIVE     = 1;
    const STATUS_EXPIRED    = 2;
    const STATUS_CANCELED   = 3;

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @return mixed
     */
    public function getStatusValue();

    /**
     * @return mixed
     */
    public static function getStatuses();
}
