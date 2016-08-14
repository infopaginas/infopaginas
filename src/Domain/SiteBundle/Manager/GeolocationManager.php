<?php

namespace Domain\SiteBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

/**
 * Class GeolocationManager
 * @package Domain\SiteBundle\Manager
 */
class GeolocationManager extends Manager
{
    private $ch;

    const   CONTENT_TYPE          = 'Content-Type: application/json';
    const   ACCEPT_TYPE           = 'Accept: application/json';

    /** @var string $googlePlacesURL */
    private $googlePlacesURL;

    /** @var string $googleAPIKey */
    private $googleAPIKey;

    /**
     * GeolocationManager constructor.
     *
     * @param EntityManager $entityManager
     * @param string $googlePlacesURL
     */
    public function __construct(EntityManager $entityManager, string $googlePlacesURL, string $googleAPIKey)
    {
        $this->em = $entityManager;

        $this->googlePlacesURL = $googlePlacesURL;
        $this->googleAPIKey    = $googleAPIKey;
    }

    /**
     * @return bool
     */
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

    /**
     * @return array
     */
    private function getContentTypes()
    {
        return array(
            self::CONTENT_TYPE,
            self::ACCEPT_TYPE
        );
    }

    /**
     * @param $term
     * @param string $lang
     * @return array
     */
    public function getGooglePlacesSuggestions($term, $lang = 'en')
    {
        $this->initCurl();

        $url = $this->getPlacesUrl($lang);
        
        $url = $url . urlencode($term);
        curl_setopt($this->ch, CURLOPT_URL, $url);

        $results = $this->getPlacesData();

        $list = array_column($results['predictions'], 'description');
        return $list;
    }

    /**
     * @return mixed
     */
    public function getPlacesData()
    {
        $result = curl_exec($this->ch);
        $info = curl_getinfo($this->ch);

        return json_decode($result, true);
    }

    /**
     * @param $locale
     * @return mixed
     */
    public function getPlacesUrl($locale)
    {
        $url = preg_replace('#google_api_key#', $this->getGoogleAPIKey(), $this->getGooglePlacesURL());
        $url = preg_replace('#locale#', $locale, $url);

        return $url;
    }

    /**
     * @return string
     */
    private function getGooglePlacesURL() : string
    {
        return $this->googlePlacesURL;
    }

    /**
     * @return string
     */
    private function getGoogleAPIKey() : string
    {
        return $this->googleAPIKey;
    }
}
