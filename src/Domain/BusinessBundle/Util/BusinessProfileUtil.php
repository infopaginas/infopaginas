<?php

namespace Domain\BusinessBundle\Util;

class BusinessProfileUtil
{
    public static function extractBusinessProfiles(array $searchResults)
    {
        return array_column($searchResults, 0);
    }
}
