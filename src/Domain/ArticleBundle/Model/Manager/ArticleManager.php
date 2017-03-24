<?php

namespace Domain\ArticleBundle\Model\Manager;

use Doctrine\ORM\EntityManager;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Model\DataType\ReviewsResultsDTO;
use Domain\SearchBundle\Model\DataType\DCDataDTO;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function fetchHomepageArticles()
    {
        $homepageArticles = $this->getRepository()->getArticlesForHomepage(self::HOMEPAGE_ARTICLES_LIMIT);

        return $homepageArticles;
    }

    public function getArticleDoubleClickData(Article $article) : DCDataDTO
    {
        return new DCDataDTO(
            [],
            '',
            [$article->getCategory()->getName()],
            $article->getSlug()
        );
    }

    public function getAllArticleDoubleClickData() : DCDataDTO
    {
        return new DCDataDTO(
            [],
            '',
            [],
            ''
        );
    }

    public function getArticleCategoryListDoubleClickData(Category $category) : DCDataDTO
    {
        return new DCDataDTO(
            [],
            '',
            [$category->getName()],
            ''
        );
    }

    /**
     * @param AbstractDTO $paramsDTO
     * @param string $categorySlug
     * @return ReviewsResultsDTO
     */
    public function getArticlesResultDTO(AbstractDTO $paramsDTO, string $categorySlug = '')
    {
        $results = $this->getRepository()->findPaginatedPublishedArticles($paramsDTO, $categorySlug);

        $totalResults = count($this->getRepository()->getPublishedArticles($categorySlug));

        $pagesCount = ceil($totalResults / $paramsDTO->limit);

        return new ReviewsResultsDTO($results, $totalResults, $paramsDTO->page, $pagesCount);
    }

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

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
                'name'  => $article->getCreatedUser()->getFullName(),
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
            true
        );

        return $url;
    }

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

    public function getArticleListSeoData($category = null)
    {
        $translator  = $this->container->get('translator');
        $seoSettings = $this->container->getParameter('seo_custom_settings');

        $companyName          = $seoSettings['company_name'];
        $titleMaxLength       = $seoSettings['title_max_length'];
        $descriptionMaxLength = $seoSettings['description_max_length'];

        $seoTitle = $translator->trans('Articles');

        if ($category) {
            $seoTitle = $seoTitle . ' - ' . $category;
        }

        $seoDescription = $seoTitle;

        $seoTitle = $seoTitle . ' | ' . $companyName;

        $seoData = [
            'seoTitle' => mb_substr($seoTitle, 0, $titleMaxLength),
            'seoDescription' => mb_substr($seoDescription, 0, $descriptionMaxLength),
        ];

        return $seoData;
    }
}
