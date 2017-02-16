<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Util\SlugUtil;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class LocalityManager extends Manager
{
    public function getLocalityNeighborhoods($locality)
    {
        if ($locality) {
            $neighborhoods = $locality->getNeighborhoods();
        } else {
            $neighborhoods = null;
        }

        return $neighborhoods;
    }

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

    public function getLocalityBySlug($localitySlug)
    {
        $customSlug = SlugUtil::convertSlug($localitySlug);

        $locality = $this->getRepository()->getLocalityBySlug($localitySlug, $customSlug);

        return $locality;
    }

    public function findAll()
    {
        $locality = $this->getRepository()->getAvailableLocalities();

        return $locality;
    }

    public function getLocalitiesByName(string $localityName, string $locale)
    {
        $localities = $this->getRepository()->getLocalitiesByNameAndLocality($localityName, $locale);

        return $localities;
    }

    public function getLocalitiesAutocomplete(string $localityName, string $locale)
    {
        $result     = [];
        $localities = $this->getLocalitiesByName($localityName, $locale);

        foreach ($localities as $locality) {
            $result[] = $locality->getTranslation('name', $locale);
        }

        return $result;
    }

    public function getCatalogLocalitiesWithContent()
    {
        $catalogLocalities = $this->getRepository()->getCatalogLocalitiesWithContent();

        return $catalogLocalities;
    }
}
