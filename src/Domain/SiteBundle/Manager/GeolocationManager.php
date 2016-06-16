<?php

namespace Domain\SiteBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class GeolocationManager extends Manager
{
    private $ch;

    const   CONTENT_TYPE          = 'Content-Type: application/json';
    const   ACCEPT_TYPE           = 'Accept: application/json';
    const   GOOGLE_API_KEY        = 'AIzaSyBBl4CQTYhUmdK4zs9EVcPmPjLBiIWez3w';

    const   GOOGLE_PLACES_URL     = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?&types=(cities)&components=country:pr&language=locale&key=google_api_key&input=';

    protected function initCurl()
    {
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 60); // 1 munute
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 600); // 10 minutes
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt(
            $this->ch,
            CURLOPT_HTTPHEADER,
            $this->getContentTypes()
        );

        return true;
    }

    private function getContentTypes()
    {
        return array(
            self::CONTENT_TYPE,
            self::ACCEPT_TYPE
        );
    }

    public function getGooglePlacesSuggestions($term, $lang = 'en')
    {
        $this->initCurl();

        $url = $this->getPlacesUrl($lang);
        
        $url = $url . $term;
        curl_setopt($this->ch, CURLOPT_URL, $url);

        $results = $this->getPlacesData();

        $list = array_column($results['predictions'], 'description');
        return $list;
    }

    public function getPlacesData()
    {
        $result = curl_exec($this->ch);
        $info = curl_getinfo($this->ch);

        return json_decode($result, true);
    }

    public function getPlacesUrl($locale)
    {
        $url = preg_replace('#google_api_key#', self::GOOGLE_API_KEY, self::GOOGLE_PLACES_URL);
        $url = preg_replace('#locale#', $locale, $url);

        return $url;
    }
}
