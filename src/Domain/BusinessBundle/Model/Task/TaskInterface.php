<?php

namespace Domain\BusinessBundle\Model\Task;

/**
 * Class TaskInterface
 * @package Domain\BusinessBundle\Model\Task
 */
interface TaskInterface
{
    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return mixed
     */
    public function getTypeName();

    /**
     * @return mixed
     */
    public function getStatusName();

    /**
     * @return mixed
     */
    public static function getTypes();

    /**
     * @return mixed
     */
    public static function getStatuses();
}
