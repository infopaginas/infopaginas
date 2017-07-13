<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Entity\LocalityPseudo;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\SearchBundle\Model\DataType\SearchDTO;
use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class LocalityManager extends Manager
{
    /**
     * @param Locality|null $locality
     *
     * @return ArrayCollection|null
     */
    public function getLocalityNeighborhoods($locality)
    {
        if ($locality) {
            $neighborhoods = $locality->getNeighborhoods();
        } else {
            $neighborhoods = null;
        }

        return $neighborhoods;
    }

    /**
     * @param string $localityName
     * @param string $locale
     *
     * @return Locality|null
     */
    public function getLocalityByNameAndLocale(string $localityName, string $locale)
    {
        if (ctype_digit(strval($localityName))) {
            // find via neighborhood by int ZIP code

            $zip = $this->em->getRepository('DomainBusinessBundle:Zip')->findOneBy(['zipCode' => $localityName]);

            if ($zip) {
                $locality = $zip->getNeighborhood()->getLocality();
            } else {
                $locality = null;
            }
        } else {
            $locality = $this->getRepository()->getLocalityByNameAndLocale($localityName, $locale);
        }

        return $locality;
    }

    /**
     * @param string $localitySlug
     *
     * @return Locality|null
     */
    public function getLocalityBySlug($localitySlug)
    {
        $customSlug = SlugUtil::convertSlug($localitySlug);

        $locality = $this->getRepository()->getLocalityBySlug($localitySlug, $customSlug);

        return $locality;
    }

    /**
     * @param string $localitySlug
     *
     * @return Locality|null
     */
    public function getLocalityByLocalityPseudoSlug($localitySlug)
    {
        $locality = $this->getRepository()->getLocalityByPseudoSlug($localitySlug);

        return $locality;
    }

    /**
     * @return Locality[]
     */
    public function findAll()
    {
        $locality = $this->getRepository()->getAvailableLocalities();

        return $locality;
    }

    /**
     * @param string $localityName
     * @param string $locale
     *
     * @return Locality[]
     */
    public function getLocalitiesByName(string $localityName, string $locale)
    {
        $localities = $this->getRepository()->getLocalitiesByNameAndLocality($localityName, $locale);

        return $localities;
    }

    /**
     * @param string $localityName
     * @param string $locale
     *
     * @return array
     */
    public function getLocalitiesAutocomplete(string $localityName, string $locale)
    {
        $result     = [];
        $localities = $this->getLocalitiesByName($localityName, $locale);

        foreach ($localities as $locality) {
            $result[] = $locality->getTranslation('name', $locale);
        }

        return $result;
    }

    /**
     * @return Locality[]
     */
    public function getCatalogLocalitiesWithContent()
    {
        $catalogLocalitiesWithContent = $this->getRepository()->getCatalogLocalitiesWithContent();

        return $catalogLocalitiesWithContent;
    }

    public function getUpdatedLocalitiesIterator()
    {
        $localities = $this->getRepository()->getUpdatedLocalitiesIterator();

        return $localities;
    }

    public function setUpdatedAllLocalities()
    {
        $data = $this->getRepository()->setUpdatedAllLocalities();

        return $data;
    }

    /**
     * @param Locality $locality
     *
     * @return array
     */
    public function buildLocalityElasticData(Locality $locality)
    {
        $enLocale   = strtolower(BusinessProfile::TRANSLATION_LANG_EN);
        $esLocale   = strtolower(BusinessProfile::TRANSLATION_LANG_ES);

        $localityEn = $locality->getTranslation(Locality::LOCALITY_FIELD_NAME, $enLocale);
        $localityEs = $locality->getTranslation(Locality::LOCALITY_FIELD_NAME, $esLocale);

        if (!$locality->getIsActive() or !$locality->getLatitude() or !$locality->getLongitude()) {
            return false;
        }

        $data = [
            'id'              => $locality->getId(),
            'auto_suggest_en' => SearchDataUtil::sanitizeElasticSearchQueryString($localityEn),
            'auto_suggest_es' => SearchDataUtil::sanitizeElasticSearchQueryString($localityEs),
            'location'             => [
                'lat' => $locality->getLatitude(),
                'lon' => $locality->getLongitude(),
            ],
        ];

        return $data;
    }

    /**
     * @param bool $sourceEnabled
     *
     * @return array
     */
    public function getLocalityElasticSearchMapping($sourceEnabled = true)
    {
        $properties = $this->getLocalityElasticSearchIndexParams();

        $data = [
            Locality::ELASTIC_DOCUMENT_TYPE => [
                '_source' => [
                    'enabled' => $sourceEnabled,
                ],
                'properties' => $properties,
            ],
        ];

        return $data;
    }

    /**
     * @return array
     */
    protected function getLocalityElasticSearchIndexParams()
    {
        $params = [
            'auto_suggest_en' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'auto_suggest_es' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'location' => [
                'type' => 'geo_point',
            ],
        ];

        return $params;
    }

    /**
     * @param SearchDTO $params
     *
     * @return array
     */
    public function getElasticClosestSearchQuery(SearchDTO $params)
    {
        $searchQuery = [
            'from' => 0,
            'size' => 1,
            'track_scores' => true,
            'sort' => [
                '_geo_distance' => [
                    'location' => [
                        'lat' => $params->locationValue->searchCenterLat,
                        'lon' => $params->locationValue->searchCenterLng,
                    ],
                    'unit' => 'mi',
                    'order' => 'asc',
                ],
                '_score' => [
                    'order' => 'desc',
                ],
            ],
        ];

        return $searchQuery;
    }

    /**
     * @param array $response
     *
     * @return array
     */
    public function getLocalityFromElasticResponse($response)
    {
        $data  = [];
        $total = 0;

        if (!empty($response['hits']['total'])) {
            $total = $response['hits']['total'];
        }

        if (!empty($response['hits']['hits'])) {
            $result = $response['hits']['hits'];
            $dataIds = [];

            foreach ($result as $item) {
                $dataIds[] = $item['_id'];
            }

            $dataRaw = $this->getRepository()->getAvailableLocalitiesByIds($dataIds);

            foreach ($dataIds as $id) {
                $item = $this->getLocalityByIdsInArray($dataRaw, $id);

                if ($item) {
                    $data[] = $item;
                }
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * @param array $data
     * @param int $id
     *
     * @return array
     */
    protected function getLocalityByIdsInArray($data, $id)
    {
        foreach ($data as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return false;
    }
}
