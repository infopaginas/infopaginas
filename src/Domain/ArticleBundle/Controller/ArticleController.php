<?php

namespace Domain\ArticleBundle\Controller;

use Domain\ArticleBundle\Model\Manager\ArticleManager;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Model\DataType\ReviewsListQueryParamsDTO;
use Domain\SearchBundle\Util\SearchDataUtil;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $articleManager = $this->getArticlesManager();
        $paramsDTO = $this->getArticleListQueryParamsDTO($request);

        $articlesResultDTO = $articleManager->getArticlesResultDTO($paramsDTO);
        $schema = $articleManager->buildArticlesSchema($articlesResultDTO->resultSet);

        $params = [
            'articlesResultDTO' => $articlesResultDTO,
            'schemaJsonLD'      => $schema,
        ];

        return $this->render(':redesign:article-list.html.twig', $params);
    }

    /**
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(string $slug)
    {
        $articleManager = $this->getArticlesManager();

        $article = $articleManager->getArticleBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException();
        }

        $schema = $articleManager->buildArticlesSchema([$article]);

        $params = [
            'article'      => $article,
            'schemaJsonLD' => $schema,
        ];

        return $this->render(':redesign:article-view.html.twig', $params);
    }

    /**
     * @param Request $request
     * @param string $categorySlug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction(Request $request, string $categorySlug)
    {
        $category = $this->getCategoryManager()->getCategoryBySlug($categorySlug);

        if ($category->getSlug() != $categorySlug) {
            return $this->handlePermanentRedirect($category);
        }

        $articleManager = $this->getArticlesManager();
        $paramsDTO = $this->getArticleListQueryParamsDTO($request);

        $articlesResultDTO = $articleManager->getArticlesResultDTO($paramsDTO, $categorySlug);

        $schema = $articleManager->buildArticlesSchema($articlesResultDTO->resultSet);

        $params = [
            'articlesResultDTO' => $articlesResultDTO,
            'articleCategory'   => $category,
            'schemaJsonLD'      => $schema,
        ];

        return $this->render(':redesign:article-list.html.twig', $params);
    }

    /**
     * @param Request $request
     * @return ReviewsListQueryParamsDTO
     */
    private function getArticleListQueryParamsDTO(Request $request) : ReviewsListQueryParamsDTO
    {
        $limit = (int)$this->get('oxa_config')->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $page = SearchDataUtil::getPageFromRequest($request);

        return new ReviewsListQueryParamsDTO($limit, $page);
    }

    /**
     * @return ArticleManager
     */
    private function getArticlesManager() : ArticleManager
    {
        return $this->get('domain_article.manager.article');
    }

    /**
     * @return CategoryManager
     */
    private function getCategoryManager() : CategoryManager
    {
        return $this->get('domain_business.manager.category');
    }

    private function handlePermanentRedirect($category)
    {
        return $this->redirectToRoute(
            'domain_article_category',
            [
                'categorySlug' => $category->getSlug(),
            ],
            301
        );
    }
}
