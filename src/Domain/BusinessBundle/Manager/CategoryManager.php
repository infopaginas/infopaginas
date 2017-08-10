<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ElasticSearchBundle\Manager\ElasticSearchManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

class CategoryManager extends Manager
{
    const AUTO_COMPLETE_TYPE  = 'category';
    const AUTO_SUGGEST_MAX_CATEGORY_COUNT = 5;
    const AUTO_SUGGEST_MAX_CATEGORY_MAIN_COUNT = 10;
    const AUTO_SUGGEST_SEPARATOR = ' ';

    /**
     * @param array $profileList
     *
     * @return array
     */
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

    /**
     * @param string $categorySlug
     *
     * @return Category|null
     */
    public function getCategoryBySlug($categorySlug)
    {
        $customSlug = SlugUtil::convertSlug($categorySlug);

        $category = $this->getRepository()->getCategoryBySlug($categorySlug, $customSlug);

        return $category;
    }

    /**
     * @param $categorySlug string
     *
     * @return Category|null
     */
    public function getCategoryByCustomSlug($categorySlug)
    {
        $category = $this->getRepository()->getCategoryByCustomSlug($categorySlug);

        return $category;
    }

    /**
     * @param Locality $locality
     * @param string|bool $locale
     *
     * @return Category[]
     */
    public function getAvailableCategoriesWithContent($locality, $locale = false)
    {
        return $this->getRepository()->getAvailableCategoriesWithContent($locality, $locale);
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    public function buildCategoryElasticData(Category $category)
    {
        if (!$category->getIsActive()) {
            return false;
        }

        $enLocale   = LocaleHelper::LOCALE_EN;
        $esLocale   = LocaleHelper::LOCALE_ES;

        $categoryEn = $category->getTranslation(Category::CATEGORY_FIELD_NAME, $enLocale);
        $categoryEs = $category->getTranslation(Category::CATEGORY_FIELD_NAME, $esLocale);

        $data = [
            'id'              => $category->getId(),
            'auto_suggest_en' => SearchDataUtil::sanitizeElasticSearchQueryString($categoryEn),
            'auto_suggest_es' => SearchDataUtil::sanitizeElasticSearchQueryString($categoryEs),
        ];

        return $data;
    }

    /**
     * @param bool $sourceEnabled
     *
     * @return array
     */
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

    /**
     * @return array
     */
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

    /**
     * @param string   $query
     * @param string   $locale
     * @param int|null $limit
     * @param int      $offset
     *
     * @return array
     */
    public function getElasticAutoSuggestSearchQuery($query, $locale, $limit = null, $offset = 0)
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

    /**
     * @param array $response
     *
     * @return array
     */
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

    /**
     * @param array $data
     * @param int $id
     *
     * @return array
     */
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
