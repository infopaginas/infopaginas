<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 31.08.16
 * Time: 21:31
 */

namespace Domain\BusinessBundle\Util\Task;

/**
 * Class ImagesChangeSetUtil
 * @package Domain\BusinessBundle\Util\Task
 */
class ImagesChangeSetUtil
{
    /**
     * @param $diff
     * @return array
     */
    public static function prepareImageDiff($diff)
    {
        $result = [
            'old' => [],
            'new' => []
        ];

        if (isset($diff->description)) {
            $result['old']['description'] = 'Description: ' . $diff->description[0];
            $result['new']['description'] = 'Description: ' . $diff->description[1];
        }

        if (isset($diff->isPrimary)) {
            $result['old']['isPrimary'] = 'Is Primary: ' . $diff->isPrimary[0];
            $result['new']['isPrimary'] = 'Is Primary: ' . $diff->isPrimary[1];
        }

        if (isset($diff->type)) {
            $result['old']['type'] = 'Type: ' . $diff->type[0];
            $result['new']['type'] = 'Type: ' . $diff->type[1];
        }

        return $result;
    }

    public static function deserializeChangeSet($value)
    {
        return json_decode($value);
    }
}
