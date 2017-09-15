<?php

namespace Domain\BusinessBundle\Util\Task;

/**
 * Class ImagesChangeSetUtil
 * @package Domain\BusinessBundle\Util\Task
 */
class ImagesChangeSetUtil
{
    /**
     * @param string
     *
     * @return mixed
     */
    public static function deserializeChangeSet($value)
    {
        return json_decode($value);
    }
}
