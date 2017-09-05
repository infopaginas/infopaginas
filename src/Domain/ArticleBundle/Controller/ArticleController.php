<?php

namespace Domain\ArticleBundle\Controller;

use Domain\ArticleBundle\Entity\Article;
use Domain\ArticleBundle\Model\Manager\ArticleManager;
use Domain\BannerBundle\Model\TypeInterface;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Manager\CategoryManager;
use Domain\BusinessBundle\Model\DataType\ReviewsListQueryParamsDTO;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $paramsDTO      = $this->getArticleListQueryParamsDTO($request);
        $locale         = LocaleHelper::getLocale($request->getLocale());

        $articlesResultDTO = $articleManager->getArticlesResultDTO($paramsDTO, $locale);
        $schema = $articleManager->buildArticlesSchema($articlesResultDTO->resultSet);

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_ARTICLE_PAGE_RIGHT,
                TypeInterface::CODE_ARTICLE_PAGE_BOTTOM,
            ]
        );

        $dcDataDTO = $articleManager->getAllArticleDoubleClickData();
        $pageData   = $articleManager->getArticleListSeoData();

        $params = [
            'results'           => $articlesResultDTO,
            'page'              => $pageData['page'],
            'seoData'           => $pageData['seoData'],
            'schemaJsonLD'      => $schema,
            'banners'           => $banners,
            'dcDataDTO'         => $dcDataDTO,
        ];

        return $this->render(':redesign:article-list.html.twig', $params);
    }

    /**
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(string $slug)
    {
       /** @var $article Article */
        $articleManager = $this->getArticlesManager();

        $article = $articleManager->getArticleBySlug($slug);

        $articleGallery = $article->getImages();

        if (!$article or !$article->getIsPublished() or ($article->getExpirationDate() and $article->isExpired())) {
            throw $this->createNotFoundException();
        }

        $schema = $articleManager->buildArticlesSchema([$article]);

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_ARTICLE_PAGE_RIGHT,
                TypeInterface::CODE_ARTICLE_PAGE_BOTTOM,
            ]
        );

        $dcDataDTO = $articleManager->getArticleDoubleClickData($article);

        $params = [
            'article'        => $article,
            'seoData'        => $article,
            'articleGallery' => $articleGallery,
            'schemaJsonLD'   => $schema,
            'banners'        => $banners,
            'dcDataDTO'      => $dcDataDTO,
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
        $paramsDTO      = $this->getArticleListQueryParamsDTO($request);
        $locale         = LocaleHelper::getLocale($request->getLocale());

        $articlesResultDTO = $articleManager->getArticlesResultDTO($paramsDTO, $locale, $categorySlug);

        $schema = $articleManager->buildArticlesSchema($articlesResultDTO->resultSet);

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_ARTICLE_PAGE_RIGHT,
                TypeInterface::CODE_ARTICLE_PAGE_BOTTOM,
            ]
        );

        $dcDataDTO = $articleManager->getArticleCategoryListDoubleClickData($category);
        $pageData   = $articleManager->getArticleListSeoData($category->getName());

        $params = [
            'results'           => $articlesResultDTO,
            'page'              => $pageData['page'],
            'seoData'           => $pageData['seoData'],
            'articleCategory'   => $category,
            'schemaJsonLD'      => $schema,
            'banners'           => $banners,
            'dcDataDTO'         => $dcDataDTO,
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

    /**
     * @param Category $category
     *
     * @return RedirectResponse
     */
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
