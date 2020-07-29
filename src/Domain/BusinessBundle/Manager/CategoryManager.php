<?php

namespace Domain\BusinessBundle\Manager;

use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Util\CategoryUtil;
use Domain\BusinessBundle\Util\SlugUtil;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class CategoryManager extends Manager
{
    public const AUTO_COMPLETE_TYPE  = 'category';
    public const AUTO_SUGGEST_MAX_CATEGORY_COUNT = 5;
    public const AUTO_SUGGEST_MAX_CATEGORY_MAIN_COUNT = 10;

    /**
     * @param array     $profileList
     * @param string    $locale
     *
     * @return array
     */
    public function getCategoriesByProfiles(array $profileList, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $businessIds = array_map(
            function ($item) {
                return $item->getId();
            },
            $profileList
        );

        return $this->getRepository()->getCategoryByBusinessesIds($businessIds, $locale);
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

    public function getFirstFoundCategory($categories): ?Category
    {
        $categories = CategoryUtil::getCategoriesNamesFromString($categories);
        $categories = CategoryUtil::getCategoriesInDifferentForms($categories);

        $categories = array_map([AdminHelper::class, 'convertAccentedString'], $categories);

        return $this->getRepository()->getCategoryByCaseInsensitiveName($categories);
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
     * @param Locality  $locality
     * @param string    $locale
     *
     * @return Category[]
     */
    public function getAvailableCategoriesWithContent($locality, $locale = LocaleHelper::DEFAULT_LOCALE)
    {
        return $this->getRepository()->getAvailableCategoriesWithContent($locality, $locale);
    }

    /**
     * @param Category $category
     *
     * @return array|bool
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
    public static function getCategoryElasticSearchIndexParams(): array
    {
        return [
            'auto_suggest_en' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
            'auto_suggest_es' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
                'fields' => [
                    'folded' => [
                        'type' => 'text',
                        'analyzer' => 'folding',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string   $query
     * @param string   $locale
     * @param int|null $limit
     * @param int      $offset
     *
     * @return array
     */
    public static function getElasticAutoSuggestSearchQuery($query, $locale, $limit = null, $offset = 0): array
    {
        if (!$limit) {
            $limit = self::AUTO_SUGGEST_MAX_CATEGORY_COUNT;
        }

        return [
            'from' => $offset,
            'size' => $limit,
            'track_scores' => true,
            'query' => [
                'multi_match' => [
                    'type' => 'most_fields',
                    'query' => $query,
                    'fields' => [
                        'auto_suggest_' . strtolower($locale),
                        'auto_suggest_' . strtolower($locale) . '.folded',
                    ],
                    'fuzziness' => 'auto',
                ],
            ],
            'sort' => [
                '_score' => [
                    'order' => 'desc'
                ],
            ],
        ];
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

            $data = $this->getAvailableCategoriesByIds($dataIds);
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * @param array $categoryIds
     *
     * @return Category[]
     */
    public function getAvailableCategoriesByIds($categoryIds)
    {
        $categories = $this->getRepository()->getAvailableCategoriesByIds($categoryIds);
        $data = [];

        foreach ($categoryIds as $id) {
            $item = $this->searchCategoryByIdsInArray($categories, $id);

            if ($item) {
                $data[] = $item;
            }
        }

        return $data;
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
