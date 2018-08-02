<?php

namespace Domain\BusinessBundle\Util;

/**
 * Class ArrayUtil
 *
 * @package Domain\BusinessBundle\Util
 */
class ArrayUtil
{
    /**
     * @param array $array
     *
     * @return array
     */
    public static function useIdInKeys(array $array)
    {
        $arrayWithIdKeys = [];

        foreach ($array as $item) {
            $arrayWithIdKeys[$item->getId()] = $item;
        }

        return $arrayWithIdKeys;
    }
}
