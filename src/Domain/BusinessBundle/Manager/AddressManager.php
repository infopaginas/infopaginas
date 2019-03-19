<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\Address\Country;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Ivory\GoogleMap\Exception\Exception;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminManager
 * @package Domain\BusinessBundle\Manager
 */
class AddressManager extends DefaultManager
{
    /**
     * @return \Ivory\GoogleMap\Services\Geocoding\Geocoder|object
     */
    protected function getGeocoder()
    {
        return $this->getContainer()->get('ivory_google_map.geocoder');
    }

    /**
     * @param $fullAddress
     */
    public function getGoogleAddresses(string $fullAddress)
    {
        $response = $this->getGeocoder()->geocode($fullAddress);

        return $response->getResults();
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public function getGoogleAddressesByCoordinates($latitude, $longitude)
    {
        $response = $this->getGeocoder()->reverse($latitude, $longitude);

        return $response->getResults();
    }

    /**
     * @param float $lat
     * @param float $lon
     *
     * @return int
     */
    public function getClosestLocalityByCoord($lat, $lon)
    {
        $container = $this->getContainer();

        $request = new Request();
        $request->query->set('clt', $lat);
        $request->query->set('clg', $lon);

        $searchManager  = $container->get('domain_search.manager.search');
        $searchDTO      = $searchManager->getLocalitySearchDTO($request);

        $businessManager = $container->get('domain_business.manager.business_profile');
        $closestLocality = $businessManager->searchClosestLocalityInElastic($searchDTO);

        return $closestLocality;
    }

    /**
     * Check if google provide valid address data for address string
     *
     * @param $address
     * @return array|bool
     */
    public function validateAddress(string $address)
    {
        $response = [
            'result' => null,
            'error' => ''
        ];

        try {
            $results = $this->getGoogleAddresses($address);
        } catch (Exception $e) {
            return $response['error'] = $e->getMessage();
        }

        $response = array_merge(
            $response,
            $this->checkGoogleResults($results)
        );

        return $response;
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return array|string
     */
    public function validateCoordinates($latitude, $longitude)
    {
        $response = [
            'result' => null,
            'error' => ''
        ];

        try {
            $results = $this->getGoogleAddressesByCoordinates($latitude, $longitude);
        } catch (Exception $e) {
            return $response['error'] = $e->getMessage();
        }

        $response = array_merge(
            $response,
            $this->checkGoogleResults($results)
        );

        return $response;
    }

    /**
     * @param $results
     * @return mixed
     */
    private function checkGoogleResults($results)
    {
        if ($results) {
            // get first address result
            // usually google returns list of addresses
            // even searching by specific address or coordinates
            // but the first one the best one (more correct)
            $result = array_shift($results);

            // data bellow is required
            // street, city, country, zip_code
            if (!$result->getAddressComponents('route') ||
                !$result->getAddressComponents('locality') ||
                !$result->getAddressComponents('country') ||
                !$result->getAddressComponents('postal_code')
            ) {
                $response['error'] = 'Invalid address. Please, be more specific';
            } else {
                // check if we get address from allowed country list
                $countries = $this->getEntityManager()->getRepository(Country::class)->getCountriesShortNames();

                $country = current($result->getAddressComponents('country'));
                if (!array_key_exists($country->getShortName(), $countries)) {
                    $response['error'] = sprintf(
                        'Country "%s" is not allowed. Must be one of: %s',
                        $country->getLongName(),
                        implode(', ', $countries)
                    );
                }
            }
            // return first address result to use it next
            $response['result'] = $result;
        } else {
            $response['error'] = 'Invalid address, no results';
        }

        return $response;
    }
}
