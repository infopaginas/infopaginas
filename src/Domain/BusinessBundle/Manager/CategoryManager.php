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
            array_map(
                function ($item) {
                    return $item->getId();
                },
                $profileList
            )
        );
    }

    public function getCategoryBySlug($slug)
    {
        return $this->getRepository()->findOneBy(['slug' => $slug]);
    }
}
