<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/14/16
 * Time: 12:02 PM
 */

namespace Domain\BusinessBundle\Manager;

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

    public function getClosestLocalityByCoord($lat, $lon){
        $request = new Request();
        $request->query->set('clt', $lat);
        $request->query->set('clg', $lon);
        $searchManager = $this->getContainer()->get('domain_search.manager.search');
        $searchDTO = $searchManager->getLoicalitySearchDTO($request);
        $closestLocality = $this->getContainer()->get('domain_business.manager.business_profile')->searchClosestLocalityInElastic($searchDTO);

        return $closestLocality;
    }

    /**
     * @param $googleAddress
     * @param BusinessProfile $businessProfile
     */
    public function setGoogleAddress($googleAddress, BusinessProfile $businessProfile)
    {
        $lat = $businessProfile->getLatitude();
        $lon = $businessProfile->getLongitude();
        // set lat and lon if it has'not been set automatically
        if (!$lat || !$lon) {
            $lat = $googleAddress->getGeometry()->getLocation()->getLatitude();
            $lon = $googleAddress->getGeometry()->getLocation()->getLongitude();
            $businessProfile->setLatitude($lat);
            $businessProfile->setLongitude($lon);
        }

        $businessProfile->setCatalogLocality($this->getClosestLocalityByCoord($lat, $lon));

        // set google address if it has'not been set automatically
        if (!$businessProfile->getGoogleAddress()) {
            $businessProfile->setGoogleAddress($googleAddress->getFormattedAddress());
        }

        $object = current($googleAddress->getAddressComponents('country'));

        if ($object) {
            $country = $this->getEntityManager()
                ->getRepository('DomainBusinessBundle:Address\Country')
                ->findOneBy(['shortName' => $object->getShortName()]);

            $businessProfile->setCountry($country);
        }

        $object = current($googleAddress->getAddressComponents('locality'));

        if ($object) {
            $businessProfile->setCity($object->getLongName());
        }

        $object = current($googleAddress->getAddressComponents('administrative_area_level_1'));

        if ($object) {
            $businessProfile->setState($object->getLongName());
        } else {
            $businessProfile->setState(null);
        }

        $object = current($googleAddress->getAddressComponents('administrative_area_level_2'));

        if ($object) {
            $businessProfile->setExtendedAddress($object->getLongName());
        } else {
            $businessProfile->setExtendedAddress(null);
        }

        $object = current($googleAddress->getAddressComponents('postal_code'));

        if ($object) {
            $businessProfile->setZipCode($object->getShortName());
        } else {
            $businessProfile->setZipCode(null);
        }

        $object = current($googleAddress->getAddressComponents('route'));

        if ($object) {
            $businessProfile->setStreetAddress($object->getLongName());
        } else {
            $businessProfile->setStreetAddress(null);
        }

        $object = current($googleAddress->getAddressComponents('street_number'));

        if ($object) {
            $businessProfile->setStreetNumber($object->getLongName());
        } else {
            $businessProfile->setStreetNumber(null);
        }
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
                $countries = $this->getEntityManager()
                    ->getRepository('DomainBusinessBundle:Address\Country')
                    ->getCountriesShortNames();

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
