<?php

namespace Domain\BusinessBundle\Utils;

class BusinessProfileUtils
{
    public static function filterLocationMarkers(array $profileList)
    {
        return json_encode(
            array_map(
                array(
                    'self',
                    'markersFilter'
                ),
                $profileList
            )
        );
    }

    protected static function markersFilter($item)
    {
        return array(
            "id" => $item['id'],
            "name" => $item['name'],
            "latitude" => $item['latitude'],
            "longitude" => $item['longitude']
        );
    }
}
