<?php

namespace Domain\BusinessBundle\Util;

class BusinessProfileUtil
{
    public static function filterLocationMarkers(array $profileList)
    {
        //dump($profileList); die;
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
            "id"            => $item[0]->getId(),
            "name"          => $item[0]->getName(),
            "address"       => $item[0]->getShortAddress(),
            "reviewsCount"  => $item[0]->getBusinessReviewsCount(),
            // "businessRaiting" => $item[0]->getBusinessReviewsRaiting(),
            "logo"          => $item[0]->getLogo(),
            "latitude"      => $item[0]->getLatitude(),
            "longitude"     => $item[0]->getLongitude()
        );
    }
}
