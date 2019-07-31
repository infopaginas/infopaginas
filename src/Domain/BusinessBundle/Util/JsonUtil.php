<?php

namespace Domain\BusinessBundle\Util;

class JsonUtil
{
    public static function htmlEntitiesEncode($data)
    {
        foreach($data as $key => $value){
            if (is_array($value)) {
                $data[$key] = self::htmlEntitiesEncode($value);
            } else {
                $data[$key] = htmlentities($value);
            }
        }

        return $data;
    }

    public static function jsonHtmlEntitiesEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}
