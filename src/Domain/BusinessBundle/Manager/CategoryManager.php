<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Util\SlugUtil;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class CategoryManager extends Manager
{
    const AUTO_COMPLETE_TYPE  = 'category';
    const AUTO_SUGGEST_MAX_CATEGORY_COUNT = 5;
    const AUTO_SUGGEST_SEPARATOR = ' ';

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

    public function getAvailableParentCategoriesWithContent($locality, $locale = false)
    {
        return $this->getRepository()->getAvailableParentCategoriesWithContent($locality, $locale);
    }

    public function searchSubcategoriesWithContentByCategory($category, $locality, $level, $locale)
    {
        $subcategoriesWithContent = $this->getRepository()
            ->searchSubcategoriesWithContentByCategory($category, $locality, $level, $locale);

        return $subcategoriesWithContent;
    }

    public function searchSubcategoryByCategory($category, $level, $locale)
    {
        return $this->getRepository()->searchSubcategoryByCategory($category, $level, $locale);
    }

    public function getCategoryParents($category)
    {
        $data = $this->getRepository()->getCategoryParents($category);
        $slugs = [
            'categorySlug1' => null,
            'categorySlug2' => null,
            'categorySlug3' => null,
        ];

        foreach ($data as $item) {
            $slugs['categorySlug' . $item->getLvl()] = $item->getSlug();
        }

        return $slugs;
    }

    public function buildCategoryElasticData(Category $category)
    {
        if (!$category->getIsActive()) {
            return false;
        }

        $categoryEn = $category->getTranslation(Category::CATEGORY_FIELD_NAME, BusinessProfile::TRANSLATION_LANG_EN);
        $categoryEs = $category->getTranslation(Category::CATEGORY_FIELD_NAME, BusinessProfile::TRANSLATION_LANG_ES);

        $data = [
            'id'              => $category->getId(),
            'auto_suggest_en' => $categoryEn,
            'auto_suggest_es' => $categoryEs,
        ];

        return $data;
    }

    public function getCategoryElasticSearchMapping($sourceEnabled = true)
    {
        $properties = $this->getCategoryElasticSearchIndexParams();

        $data = [
            Category::ELASTIC_DOCUMENT_TYPE => [
                '_source' => [
                    'enabled' => $sourceEnabled,
                ],
                'properties' => $properties,
            ],
        ];

        return $data;
    }

    public function getUpdatedCategoriesIterator()
    {
        $categories = $this->getRepository()->getUpdatedCategoriesIterator();

        return $categories;
    }

    public function setUpdatedAllCategories()
    {
        $data = $this->getRepository()->setUpdatedAllCategories();

        return $data;
    }

    protected function getCategoryElasticSearchIndexParams()
    {
        $params = [
            'auto_suggest_en' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'auto_suggest_es' => [
                'type' => 'string',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'string',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
        ];

        return $params;
    }

    public function getElasticAutoSuggestSearchQuery($query, $locale, $limit = false, $offset = 0)
    {
        if (!$limit) {
            $limit = self::AUTO_SUGGEST_MAX_CATEGORY_COUNT;
        }

        $searchQuery = [
            'from' => $offset,
            'size' => $limit,
            'track_scores' => true,
            'query' => [
                'multi_match' => [
                    'type' => 'most_fields',
                    'query' => $query,
                    'fields' => [
                        'auto_suggest_' . strtolower($locale),
                        'auto_suggest_' . strtolower($locale) . '.folded'
                    ],
                ],
            ],
            'sort' => [
                '_score' => [
                    'order' => 'desc'
                ],
            ],
        ];

        return $searchQuery;
    }

    public function getCategoryFromElasticResponse($response)
    {
        $data  = [];
        $total = 0;

        if (!empty($response['hits']['total'])) {
            $total = $response['hits']['total'];
        }

        if (!empty($response['hits']['hits'])) {
            $result = $response['hits']['hits'];
            $dataIds = [];

            foreach ($result as $item) {
                $dataIds[] = $item['_id'];
            }

            $dataRaw = $this->getRepository()->getAvailableCategoriesByIds($dataIds);

            foreach ($dataIds as $id) {
                $item = $this->searchCategoryByIdsInArray($dataRaw, $id);

                if ($item) {
                    $data[] = $item;
                }
            }
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    protected function searchCategoryByIdsInArray($data, $id)
    {
        foreach ($data as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return false;
    }
}
