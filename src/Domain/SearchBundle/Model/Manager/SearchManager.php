<?php

namespace Domain\SearchBundle\Model\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\GeolocationBundle\Model\Geolocation\LocationValueObject;

class SearchManager extends Manager
{
    public function getAutocompleteDataByPhrase(string $phrase)
    {

    }

    public function getAutocompleteDataByLocation(LocationValueObject $location)
    {

    }

    public function getAutocompleteDataByPhraseAndLocation(string $phrase, LocationValueObject $location)
    {

    }

    public function searchByPhrase(string $phrase)
    {

    }

    public function searchByLocation(LocationValueObject $location)
    {

    }

    public function searchByPhraseAndLocation(string $phrase, string $location)
    {
        $this->getRepository()->getSearchQuery($phrase, $location);
    }
}
