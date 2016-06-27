<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class BusinessProfileManager extends Manager
{
    public function searchByPhraseAndLocation(string $phrase, string $location)
    {
        return $this->getRepository()->search($phrase, $location);
    }

    public function searchAutosuggestByPhraseAndLocation(string $phrase, string $location)
    {
        return $this->getRepository()->searchAutosuggest($phrase, $location);
    }
}
