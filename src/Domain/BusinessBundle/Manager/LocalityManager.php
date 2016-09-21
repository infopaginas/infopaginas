<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class LocalityManager extends Manager
{
    public function getNeighborhoodLocationsByLocalityName(string $localityName)
    {
        return $this->getRepository()->getNeighborhoodToLocalityByName($localityName);
    }

    public function getLocalityByNameAndLocale(string $localityName, $locale)
    {
        $locality = null;

        // todo - get for $locale

        if (1) {
            $locality = $this->getRepository()->findOneBy(['name' => $localityName]);
        }

        return $locality;
    }
}
