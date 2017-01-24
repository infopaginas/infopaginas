<?php

namespace Domain\BusinessBundle\Model;

/**
 * Class StatusInterface
 * @package Domain\BusinessBundle\Model
 */
interface StatusInterface
{
    const PROPERTY_NAME_STATUS  = 'status';

    const STATUS_ACTIVE     = 1;
    const STATUS_EXPIRED    = 2;
    const STATUS_CANCELED   = 3;
    const STATUS_PENDING    = 4;

    /**
     * @param integer $status
     * @return $this
     */
    public function setStatus($status);

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
