<?php

namespace Domain\BusinessBundle\Util;

class BusinessProfileUtil
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
            "id"            => $item->getId(),
            "name"          => $item->getName(),
            "address"       => $item->getShortAddress(),
            "reviewsCount"  => $item->getBusinessReviewsCount(),
            "logo"          => $item->getLogo(),
            "latitude"      => $item->getLatitude(),
            "longitude"     => $item->getLongitude()
        );
    }


    public static function extractBusinessProfiles(array $searchResults)
    {
        return array_column($searchResults, 0);
    }
}
