<?php

namespace Domain\ArticleBundle\Model\Manager;

use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Model\DataType\ReviewsResultsDTO;
use Domain\BusinessBundle\Util\JsonUtil;
use Domain\PageBundle\Entity\Page;
use Domain\SearchBundle\Model\DataType\DCDataDTO;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ArticleManager
 * Article management entry point
 *
 * @package Domain\ArticleBundle\Manager
 */
class ArticleManager extends Manager
{
    const HOMEPAGE_ARTICLES_LIMIT = 4;

    /** @var ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param string $locale
     *
     * @return Article[]
     */
    public function fetchHomepageArticles($locale = LocaleHelper::DEFAULT_LOCALE)
    {
        $homepageArticles = $this->getRepository()->getArticlesForHomepage(self::HOMEPAGE_ARTICLES_LIMIT, $locale);

        return $homepageArticles;
    }

    /**
     * @param Article $article
     *
     * @return DCDataDTO
     */
    public function getArticleDoubleClickData(Article $article) : DCDataDTO
    {
        return new DCDataDTO(
            [],
            '',
            [
                $article->getCategory()->getSlug(),
            ],
            $article->getSlug()
        );
    }

    /**
     * @return DCDataDTO
     */
    public function getAllArticleDoubleClickData() : DCDataDTO
    {
        return new DCDataDTO(
            [],
            '',
            [],
            ''
        );
    }

    /**
     * @param Category $category
     *
     * @return DCDataDTO
     */
    public function getArticleCategoryListDoubleClickData(Category $category) : DCDataDTO
    {
        return new DCDataDTO(
            [],
            '',
            [
                $category->getSlug(),
            ],
            ''
        );
    }

    /**
     * @param AbstractDTO $paramsDTO
     * @param string $locale
     * @param string $categorySlug
     *
     * @return ReviewsResultsDTO
     */
    public function getArticlesResultDTO(AbstractDTO $paramsDTO, $locale = LocaleHelper::DEFAULT_LOCALE, string $categorySlug = '')
    {
        $results = $this->getRepository()->findPaginatedPublishedArticles($paramsDTO, $categorySlug, $locale);

        $totalResults = count($this->getRepository()->getPublishedArticles($categorySlug));

        $pagesCount = ceil($totalResults / $paramsDTO->limit);

        return new ReviewsResultsDTO($results, $totalResults, $paramsDTO->page, $pagesCount);
    }

    /**
     * @param string $slug
     *
     * @return Article|null
     */
    public function getArticleBySlug($slug)
    {
        return $this->getRepository()->findOneBy(['slug' => $slug]);
    }

    /**
     * see https://developers.google.com/search/docs/data-types/articles
     * @param Article[]  $articles
     *
     * @return string
     */
    public function buildArticlesSchema($articles)
    {
        $schema = [];

        foreach ($articles as $article) {
            $schema[] = $this->buildBaseArticleSchema($article);
        }

        return JsonUtil::jsonHtmlEntitiesEncode(JsonUtil::htmlEntitiesEncode($schema));
    }

    /**
     * @param Article $article
     *
     * @return array
     */
    private function buildBaseArticleSchema($article)
    {
        $schemaItem = [
            '@context' => 'http://schema.org',
            '@type'    => 'Article',
            'headline' => $article->getTitle(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => $this->getArticleUrl($article),
            ],
            'datePublished' => $article->getCreatedAt()->format('c'),
            'description'   => $article->getBody(),
            'author' => [
                '@type' => 'Person',
                'name'  => $article->getAuthorName(),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => 'Infopaginas',
                'logo'  => $this->getPublisherLogo(),
            ]
        ];

        if ($article->getUpdatedAt()) {
            $schemaItem['dateModified'] = $article->getUpdatedAt()->format('c');
        }

        $image = $article->getImage();
        if ($image) {
            $businessProfileManager = $this->container->get('domain_business.manager.business_profile');
            $imageUrl = $businessProfileManager->getMediaPublicUrl($image, 'preview');
            $schemaItem['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $imageUrl,
                'width'  => (string)$image->getWidth(),
                'height' => (string)$image->getHeight(),
            ];
        }

        return $schemaItem;
    }

    /**
     * @param Article $article
     *
     * @return string
     */
    public function getArticleUrl($article)
    {
        $url = $this->container->get('router')->generate(
            'domain_article_view',
            [
                'slug' => $article->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return $url;
    }

    /**
     * @return array
     */
    private function getPublisherLogo()
    {
        $request = $this->container->get('request');

        $image = $this->container->getParameter('default_image');
        $url = $request->getScheme() . '://' . $request->getHost() . $image['path'] . $image['article']['image'];

        $logo = [
            '@type'  => 'ImageObject',
            'url'    => $url,
            'width'  => $image['article']['width'],
            'height' => $image['article']['height'],
        ];

        return $logo;
    }

    /**
     * @param string $category
     *
     * @return array
     */
    public function getArticleListSeoData($category = '')
    {
        $pageManager = $this->container->get('domain_page.manager.page');

        if ($category) {
            $pageCode = Page::CODE_ARTICLE_CATEGORY_LIST;
            $data = [
                '[category]' => $category,
            ];
        } else {
            $pageCode = Page::CODE_ARTICLE_LIST;
            $data = [];
        }

        $page    = $pageManager->getPageByCode($pageCode);
        $seoData = $pageManager->getPageSeoData($page, $data);

        return [
            'seoData' => $seoData,
            'page'    => $page,
        ];
    }
}
