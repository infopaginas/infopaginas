<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class CategoryManager extends Manager
{
    public function searchAutosuggestByName($name)
    {
        return $this->getRepository()->searchAutosuggest($name);
    }

    public function getCategoriesByProfiles(array $profileList)
    {
        return $this->getRepository()->getCategoryByBusinessesIds(
            array_column($profileList, 'id')
        );
    }
}
