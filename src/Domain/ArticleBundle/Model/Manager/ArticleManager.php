<?php

namespace Domain\ArticleBundle\Model\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Model\DataType\ReviewsResultsDTO;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

/**
 * Class ArticleManager
 * Article management entry point
 *
 * @package Domain\ArticleBundle\Manager
 */
class ArticleManager extends Manager
{
    const HOMEPAGE_ARTICLES_LIMIT = 2;

    public function fetchHomepageArticles()
    {
        $homepageArticles = $this->getRepository()->getArticlesForHomepage(self::HOMEPAGE_ARTICLES_LIMIT);

        return $homepageArticles;
    }

    /**
     * @param AbstractDTO $paramsDTO
     * @param string $category
     * @return ReviewsResultsDTO
     */
    public function getArticlesResultDTO(AbstractDTO $paramsDTO, string $category = '')
    {
        $results = $this->getRepository()->findPaginatedPublishedArticles($paramsDTO, $category);

        $totalResults = count($this->getRepository()->getPublishedArticles($category));

        $pagesCount = ceil($totalResults/$paramsDTO->limit);

        return new ReviewsResultsDTO($results, $totalResults, $paramsDTO->page, $pagesCount);
    }

    public function getArticleBySlug($slug)
    {
        return $this->getRepository()->findOneBy(['slug' => $slug]);
    }
}
