<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class CategoryManager extends Manager
{
    public function searchAutosuggestByName(string $name, string $locale)
    {
        return $this->getRepository()->searchAutosuggest($name, $locale);
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

    public function getCategoryBySlug($categorySlug)
    {
        return $this->getRepository()->findOneBy(['slug' => $categorySlug]);
    }

    public function getAvailableParentCategories()
    {
        return $this->getRepository()->getAvailableParentCategories();
    }

    public function searchSubcategoryByCategory($category, $locale)
    {
        return $this->getRepository()->searchSubcategoryByCategory($category, $locale);
    }
}
