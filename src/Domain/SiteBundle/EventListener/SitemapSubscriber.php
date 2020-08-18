<?php

namespace Domain\SiteBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\CatalogItem;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Domain\EmergencyBundle\Entity\EmergencyCatalogItem;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\RequestContext;

class SitemapSubscriber implements EventSubscriberInterface
{
    const SECTION_MAIN                        = 'main';
    const SECTION_ARTICLE                     = 'article';
    const SECTION_CATALOG                     = 'catalog';
    const SECTION_EMERGENCY_CATALOG           = 'emergency_catalog';
    const SECTION_BUSINESS_PROFILES           = 'businessProfiles';
    const SECTION_BUSINESS_PROFILE_DIRECTIONS = 'businessProfileDirections';

    /**
     * @var ContainerInterface
     */
    private $container;

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
    private $languages = [];

    /**
     * @var RequestContext
     */
    private $context;

    /**
     * @var string
     */
    private $defaultHost;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
        $this->urlGenerator = $container->get('router');
        $this->manager      = $container->get('doctrine.orm.entity_manager');
        $this->context      = $this->urlGenerator->getContext();
        $this->defaultHost  = $this->context->getHost();
        $this->config       = $container->get('oxa_config');
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
        $this->addEmergencyBusinessCatalog();
        $this->addStaticUrls();
    }

    protected function addBusinessProfiles()
    {
        $businessProfiles = $this->manager->getRepository(BusinessProfile::class)->getActiveBusinessProfilesIterator();

        foreach ($businessProfiles as $row) {
            /* @var $businessProfile \Domain\BusinessBundle\Entity\BusinessProfile */
            $businessProfile = current($row);

            $this->context->setHost($this->defaultHost);

            $loc = $this->urlGenerator->generate(
                'domain_business_profile_view',
                [
                    'citySlug' => $businessProfile->getCitySlug(),
                    'slug'     => $businessProfile->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $locDirections = $this->urlGenerator->generate(
                'domain_search_show_directions',
                [
                    'slug'     => $businessProfile->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $lastModify      = $businessProfile->getUpdatedAt();
            $priority        = $this->getBusinessProfilePriority($businessProfile);
            $changeFrequency = null;

            $baseUrl = new UrlConcrete($loc, $lastModify, $changeFrequency, $priority);
            $baseDirectionsUrl = new UrlConcrete($locDirections, $lastModify, $changeFrequency, $priority);

            if ($this->languages) {
                $urlLang = new GoogleMultilangUrlDecorator($baseUrl);
                $directionsUrl = new GoogleMultilangUrlDecorator($baseDirectionsUrl);

                foreach ($this->languages as $locale) {
                    $this->context->setHost($locale . '.' . $this->defaultHost);

                    $url = $this->urlGenerator->generate(
                        'domain_business_profile_view',
                        [
                            'citySlug' => $businessProfile->getCitySlug(),
                            'slug'     => $businessProfile->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $directionsUrl = $this->urlGenerator->generate(
                        'domain_search_show_directions',
                        ['slug' => $businessProfile->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $urlLang->addLink($url, $locale);
                    $directionsUrl->addLink($directionsUrl, $locale);
                }

                $baseUrl = $urlLang;
                $baseDirectionsUrl = $directionsUrl;
            }

            $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_BUSINESS_PROFILES);
            $this->siteMapEvent->getUrlContainer()->addUrl($baseDirectionsUrl, self::SECTION_BUSINESS_PROFILE_DIRECTIONS);

            $this->manager->clear(BusinessProfile::class);
        }
    }

    protected function addBusinessProfilesCatalog()
    {
        $catalogItems = $this->manager->getRepository(CatalogItem::class)->getCatalogItemsWithContentIterator();

        $this->addCatalogUrl();

        foreach ($catalogItems as $row) {
            /* @var $catalogItem \Domain\BusinessBundle\Entity\CatalogItem */
            $catalogItem = current($row);

            $this->addCatalogUrlByCatalogItem($catalogItem);

            $this->manager->clear(CatalogItem::class);
            unset($catalogItem);
        }
    }

    protected function addEmergencyBusinessCatalog()
    {
        if ($this->getEmergencyFeatureEnabled()) {
            $catalogItems = $this->manager->getRepository(EmergencyCatalogItem::class)->getCatalogItemsWithContentIterator();

            foreach ($catalogItems as $row) {
                /* @var $catalogItem \Domain\EmergencyBundle\Entity\EmergencyCatalogItem */
                $catalogItem = current($row);

                $this->addEmergencyCatalogUrlByCatalogItem($catalogItem);

                $this->manager->clear(EmergencyCatalogItem::class);
                unset($catalogItem);
            }
        }
    }

    protected function addArticleList()
    {
        $articles = $this->manager->getRepository(Article::class)->getActiveArticlesIterator();

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

            $this->manager->clear(Article::class);
        }
    }

    protected function addArticleCategoryList()
    {
        $categories = $this->manager->getRepository(Category::class)->getAvailableCategoriesIterator();

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

            $this->manager->clear(Category::class);
        }
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return float
     */
    protected function getBusinessProfilePriority(BusinessProfile $businessProfile)
    {
        $code = $businessProfile->getSubscriptionPlanCode();

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

    protected function addCatalogUrl($catalogLocalitySlug = null, $categorySlug = null)
    {
        $this->context->setHost($this->defaultHost);

        $loc = $this->urlGenerator->generate(
            'domain_search_catalog',
            [
                'localitySlug' => $catalogLocalitySlug,
                'categorySlug' => $categorySlug,
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
                        'localitySlug' => $catalogLocalitySlug,
                        'categorySlug' => $categorySlug,
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $urlLang->addLink($url, $locale);
            }

            $baseUrl = $urlLang;
        }

        $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_CATALOG);
    }

    /**
     * @param string $areaSlug
     * @param string $categorySlug
     */
    protected function addEmergencyCatalogUrl($areaSlug, $categorySlug)
    {
        $this->context->setHost($this->defaultHost);

        $loc = $this->urlGenerator->generate(
            'emergency_catalog',
            [
                'areaSlug'     => $areaSlug,
                'categorySlug' => $categorySlug,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $baseUrl = new UrlConcrete($loc);

        if ($this->languages) {
            $urlLang = new GoogleMultilangUrlDecorator($baseUrl);

            foreach ($this->languages as $locale) {
                $this->context->setHost($locale . '.' . $this->defaultHost);

                $url = $this->urlGenerator->generate(
                    'emergency_catalog',
                    [
                        'areaSlug'     => $areaSlug,
                        'categorySlug' => $categorySlug,
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $urlLang->addLink($url, $locale);
            }

            $baseUrl = $urlLang;
        }

        $this->siteMapEvent->getUrlContainer()->addUrl($baseUrl, self::SECTION_EMERGENCY_CATALOG);
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
        $statisticUrls = [
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

        if ($this->getEmergencyFeatureEnabled()) {
            $statisticUrls['emergency_main'] = [
                'route'   => 'emergency_main',
                'section' => self::SECTION_MAIN,
            ];
        }

        return $statisticUrls;
    }

    private function addCatalogUrlByCatalogItem(CatalogItem $catalogItem)
    {
        $locality = $catalogItem->getLocality();
        $category = $catalogItem->getCategory();

        $localitySlug = $locality->getSlug();
        $categorySlug = null;

        if ($category) {
            $categorySlug = $category->getSlug();
        }

        $this->addCatalogUrl($localitySlug, $categorySlug);

        unset($locality, $category, $localitySlug, $categorySlug);
    }

    /**
     * @param EmergencyCatalogItem $catalogItem
     */
    private function addEmergencyCatalogUrlByCatalogItem(EmergencyCatalogItem $catalogItem)
    {
        $area     = $catalogItem->getArea();
        $category = $catalogItem->getCategory();

        if ($category) {
            $areaSlug     = $area->getSlug();
            $categorySlug = $category->getSlug();

            $this->addEmergencyCatalogUrl($areaSlug, $categorySlug);
        }

        unset($locality, $category, $localitySlug, $categorySlug);
    }

    /**
     * @return bool
     */
    protected function getEmergencyFeatureEnabled()
    {
        return (bool)$this->config->getSetting(ConfigInterface::EMERGENCY_SITUATION_ON)->getValue();
    }
}
