<?php

namespace Domain\BusinessBundle\Manager;

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
        return $this->getRepository()->getLocalityByNameAndLocale($localityName, $locale);
    }
}
