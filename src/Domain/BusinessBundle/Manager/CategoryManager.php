<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\Category;
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

    public function getAvailableParentCategories($locale = false)
    {
        return $this->getRepository()->getAvailableParentCategories($locale);
    }

    public function searchSubcategoryByCategory($category, $level, $locale)
    {
        return $this->getRepository()->searchSubcategoryByCategory($category, $level, $locale);
    }

    public function getCategoryParents($category)
    {
        $data = $this->getRepository()->getCategoryParents($category);
        $slugs = [
            'categorySlug'    => null,
            'categorySlug2'   => null,
            'categorySlug3'   => null,
        ];

        foreach ($data as $item) {
            if ($item->getLvl() == Category::CATEGORY_LEVEL_1) {
                $slugs['categorySlug'] = $item->getSlug();
            } else {
                $slugs['categorySlug' . $item->getLvl()] = $item->getSlug();
            }
        }

        return $slugs;
    }
}
