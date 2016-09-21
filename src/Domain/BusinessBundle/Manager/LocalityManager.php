<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class LocalityManager extends Manager
{
    public function getNeighborhoodLocationsByLocalityName(string $localityName)
    {
        return $this->getRepository()->getNeighborhoodToLocalityByName($localityName);
    }

    public function getLocalityByNameAndLocale(string $localityName, string $locale)
    {
        return $this->getRepository()->getLocalityByNameAndLocale($localityName, $locale);
    }
}
