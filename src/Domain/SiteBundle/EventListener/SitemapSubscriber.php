<?php

namespace Domain\SiteBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\RequestContext;

class SitemapSubscriber implements EventSubscriberInterface
{
    const SECTION_MAIN              = 'main';
    const SECTION_ARTICLE           = 'article';
    const SECTION_CATALOG           = 'catalog';
    const SECTION_BUSINESS_PROFILES = 'businessProfiles';

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var SitemapPopulateEvent
     */
    private $siteMapEvent;

    /**
     * @var array
     */
    private $languages = ['en'];

    /**
     * @var RequestContext
     */
    private $context;

    /**
     * @var string
     */
    private $defaultHost;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ObjectManager         $manager
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, ObjectManager $manager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->manager      = $manager;
        $this->context      = $this->urlGenerator->getContext();
        $this->defaultHost  = $this->context->getHost();
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'registerDynamicUrls',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function registerDynamicUrls(SitemapPopulateEvent $event)
    {
        $this->siteMapEvent = $event;

        $this->addBusinessProfiles();
        $this->addArticleList();
        $this->addArticleCategoryList();
        $this->addBusinessProfilesCatalog();
        $this->addStaticUrls();
    }

    protected function addBusinessProfiles()
    {
        $businessProfiles = $this->manager->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getActiveBusinessProfilesIterator();

        foreach ($businessProfiles as $row) {
            /* @var $businessProfile \Domain\BusinessBundle\Entity\BusinessProfile */
            $businessProfile = current($row);

            $this->context->setHost($this->defaultHost);

            $loc = $this->urlGenerator->generate(
                'domain_business_profile_view',
                [
                    'citySlug' => $businessProfile->getCatalogLocality()->getSlug(),
                    'slug'     => $businessProfile->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $lastModify      = $businessProfile->getUpdatedAt();
            $priority        = $this->getBusinessProfilePriority($businessProfile);
            $changeFrequency = null;

            $baseUrl = new UrlConcrete($loc, $lastModify, $changeFrequency, $priority);

            if ($this->languages) {
                $urlLang = new GoogleMultilangUrlDecorator($baseUrl);

                foreach ($this->languages as $locale) {
                    $this->context->setHost($locale . '.' . $this->defaultHost);

                    $url = $this->urlGenerator->generate(
                        'domain_business_profile_view',
                        [
                            'citySlug' => $businessProfile->getCatalogLocality()->getSlug(),
                            'slug'     => $businessProfile->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $urlLang->addLink($url, $locale);
                }

                $baseUrl = $urlLang;
            }

            $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_BUSINESS_PROFILES);

            $this->manager->detach($row[0]);
        }
    }

    protected function addBusinessProfilesCatalog()
    {
        $catalogLocalities = $this->manager->getRepository('DomainBusinessBundle:Locality')
            ->getAvailableLocalitiesIterator();

        $this->addCatalogUrl();

        foreach ($catalogLocalities as $localityRow) {
            /* @var $catalogLocality \Domain\BusinessBundle\Entity\Locality */
            $catalogLocality = current($localityRow);

            $this->addCatalogUrl($catalogLocality->getSlug());

            $categories = $this->manager->getRepository('DomainBusinessBundle:Category')
                ->getAvailableParentCategoriesIterator();

            foreach ($categories as $categoryRow1) {
                /* @var $category1 \Domain\BusinessBundle\Entity\Category */
                $category1 = current($categoryRow1);

                $this->addCatalogUrl($catalogLocality->getSlug(), $category1->getSlug());

                $categories2 = $this->manager->getRepository('DomainBusinessBundle:Category')
                    ->getAvailableSubcategoriesByCategoryIterator($category1, Category::CATEGORY_LEVEL_2);

                foreach ($categories2 as $categoryRow2) {
                    /* @var $subcategory \Domain\BusinessBundle\Entity\Category */
                    $category2 = current($categoryRow2);

                    $this->addCatalogUrl($catalogLocality->getSlug(), $category1->getSlug(), $category2->getSlug());

                    $categories3 = $this->manager->getRepository('DomainBusinessBundle:Category')
                        ->getAvailableSubcategoriesByCategoryIterator($category2, Category::CATEGORY_LEVEL_3);

                    foreach ($categories3 as $categoryRow3) {
                        /* @var $subcategory \Domain\BusinessBundle\Entity\Category */
                        $category3 = current($categoryRow3);

                        $this->addCatalogUrl(
                            $catalogLocality->getSlug(),
                            $category1->getSlug(),
                            $category2->getSlug(),
                            $category3->getSlug()
                        );

                        $this->manager->detach($categoryRow3[0]);
                    }

                    $this->manager->detach($categoryRow2[0]);
                }

                $this->manager->detach($categoryRow1[0]);
            }

            $this->manager->detach($localityRow[0]);
        }
    }

    protected function addArticleList()
    {
        $articles = $this->manager->getRepository('DomainArticleBundle:Article')
            ->getActiveArticlesIterator();

        foreach ($articles as $row) {
            /* @var $article \Domain\ArticleBundle\Entity\Article */
            $article = current($row);

            $this->context->setHost($this->defaultHost);

            $loc = $this->urlGenerator->generate(
                'domain_article_view',
                [
                    'slug' => $article->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $lastModify      = $article->getUpdatedAt();
            $changeFrequency = null;
            $priority        = null;

            $baseUrl = new UrlConcrete($loc, $lastModify, $changeFrequency, $priority);

            if ($this->languages) {
                $urlLang = new GoogleMultilangUrlDecorator($baseUrl);

                foreach ($this->languages as $locale) {
                    $this->context->setHost($locale . '.' . $this->defaultHost);

                    $url = $this->urlGenerator->generate(
                        'domain_article_view',
                        [
                            'slug' => $article->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $urlLang->addLink($url, $locale);
                }

                $baseUrl = $urlLang;
            }

            $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_ARTICLE);

            $this->manager->detach($row[0]);
        }
    }

    protected function addArticleCategoryList()
    {
        $categories = $this->manager->getRepository('DomainBusinessBundle:Category')
            ->getAvailableCategoriesIterator();

        foreach ($categories as $row) {
            /* @var $category \Domain\BusinessBundle\Entity\Category */
            $category = current($row);

            $this->context->setHost($this->defaultHost);

            //article categories
            $loc = $this->urlGenerator->generate(
                'domain_article_category',
                [
                    'categorySlug' => $category->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $baseUrl = new UrlConcrete($loc);

            if ($this->languages) {
                $urlLang = new GoogleMultilangUrlDecorator($baseUrl);

                foreach ($this->languages as $locale) {
                    $this->context->setHost($locale . '.' . $this->defaultHost);

                    $url = $this->urlGenerator->generate(
                        'domain_article_category',
                        [
                            'categorySlug' => $category->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $urlLang->addLink($url, $locale);
                }

                $baseUrl = $urlLang;
            }

            $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_ARTICLE);

            $this->manager->detach($row[0]);
        }
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return float
     */
    protected function getBusinessProfilePriority(BusinessProfile $businessProfile)
    {
        $code = $businessProfile->getSubscription()->getSubscriptionPlan()->getCode();

        switch ($code) {
            case SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM:
                $priority = 1.0;
                break;
            case SubscriptionPlanInterface::CODE_PREMIUM_GOLD:
                $priority = 0.7;
                break;
            case SubscriptionPlanInterface::CODE_PREMIUM_PLUS:
                $priority = 0.5;
                break;
            case SubscriptionPlanInterface::CODE_PRIORITY:
                $priority = 0.2;
                break;
            default:
                $priority = 0.0;
                break;
        }

        return $priority;
    }

    protected function addCatalogUrl(
        $catalogLocalitySlug = null,
        $categorySlug1 = null,
        $categorySlug2 = null,
        $categorySlug3 = null
    ) {
        $this->context->setHost($this->defaultHost);

        $loc = $this->urlGenerator->generate(
            'domain_search_catalog',
            [
                'localitySlug'  => $catalogLocalitySlug,
                'categorySlug1' => $categorySlug1,
                'categorySlug2' => $categorySlug2,
                'categorySlug3' => $categorySlug3,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $baseUrl = new UrlConcrete($loc);

        if ($this->languages) {
            $urlLang = new GoogleMultilangUrlDecorator($baseUrl);

            foreach ($this->languages as $locale) {
                $this->context->setHost($locale . '.' . $this->defaultHost);

                $url = $this->urlGenerator->generate(
                    'domain_search_catalog',
                    [
                        'localitySlug'  => $catalogLocalitySlug,
                        'categorySlug1' => $categorySlug1,
                        'categorySlug2' => $categorySlug2,
                        'categorySlug3' => $categorySlug3,
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $urlLang->addLink($url, $locale);
            }

            $baseUrl = $urlLang;
        }

        $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_CATALOG);
    }

    public function addStaticUrls()
    {
        $data = $this->getStaticUrls();

        foreach ($data as $item) {
            $this->context->setHost($this->defaultHost);

            $loc = $this->urlGenerator->generate($item['route'], [], UrlGeneratorInterface::ABSOLUTE_URL);

            $baseUrl = new UrlConcrete($loc);

            if ($this->languages) {
                $urlLang = new GoogleMultilangUrlDecorator($baseUrl);

                foreach ($this->languages as $locale) {
                    $this->context->setHost($locale . '.' . $this->defaultHost);

                    $url = $this->urlGenerator->generate($item['route'], [], UrlGeneratorInterface::ABSOLUTE_URL);

                    $urlLang->addLink($url, $locale);
                }

                $baseUrl = $urlLang;
            }

            $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, $item['section']);
        }
    }

    private function getStaticUrls()
    {
        return [
            'domain_article_homepage' => [
                'route'   => 'domain_article_homepage',
                'section' => self::SECTION_ARTICLE,
            ],
            'domain_site_home_index' => [
                'route'   => 'domain_site_home_index',
                'section' => self::SECTION_MAIN,
            ],
            'domain_page_view_contact' => [
                'route'   => 'domain_page_view_contact',
                'section' => self::SECTION_MAIN,
            ],
            'domain_page_view_terms' => [
                'route'   => 'domain_page_view_terms',
                'section' => self::SECTION_MAIN,
            ],
            'domain_page_view_privacy' => [
                'route'   => 'domain_page_view_privacy',
                'section' => self::SECTION_MAIN,
            ],
            'domain_page_view_advertise' => [
                'route'   => 'domain_page_view_advertise',
                'section' => self::SECTION_MAIN,
            ],
            'domain_page_view_features' => [
                'route'   => 'domain_page_view_features',
                'section' => self::SECTION_MAIN,
            ],
            'domain_business_video_list' => [
                'route'   => 'domain_business_video_list',
                'section' => self::SECTION_MAIN,
            ],
        ];
    }
}
