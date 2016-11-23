<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Util\SlugUtil;
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
        $customSlug = SlugUtil::convertSlug($categorySlug);

        $category = $this->getRepository()->getCategoryBySlug($categorySlug, $customSlug);

        return $category;
    }

    public function getAvailableParentCategories()
    {
        return $this->getRepository()->getAvailableParentCategories();
    }
}
