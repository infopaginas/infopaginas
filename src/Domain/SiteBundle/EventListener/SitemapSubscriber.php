<?php

namespace Domain\SiteBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapSubscriber implements EventSubscriberInterface
{
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
     * @param UrlGeneratorInterface $urlGenerator
     * @param ObjectManager         $manager
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, ObjectManager $manager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->manager      = $manager;
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
    }

    protected function addBusinessProfiles()
    {
        $businessProfiles = $this->manager->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getActiveBusinessProfilesIterator();

        foreach ($businessProfiles as $row) {
            /* @var $businessProfile \Domain\BusinessBundle\Entity\BusinessProfile */
            $businessProfile = current($row);

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

            $this->siteMapEvent->getUrlContainer()->addUrl(
                new UrlConcrete($loc, $lastModify, $changeFrequency, $priority),
                'businessProfiles'
            );

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

            foreach ($categories as $categoryRow) {
                /* @var $category \Domain\BusinessBundle\Entity\Category */
                $category = current($categoryRow);

                $this->addCatalogUrl($catalogLocality->getSlug(), $category->getSlug());

                $subcategories = $categories = $this->manager->getRepository('DomainBusinessBundle:Category')
                    ->getAvailableSubcategoriesByCategoryIterator($category);

                foreach ($subcategories as $subcategoryRow) {
                    /* @var $subcategory \Domain\BusinessBundle\Entity\Category */
                    $subcategory = current($subcategoryRow);

                    $this->addCatalogUrl($catalogLocality->getSlug(), $category->getSlug(), $subcategory->getSlug());

                    $this->manager->detach($subcategoryRow[0]);
                }

                $this->manager->detach($categoryRow[0]);
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

            //article
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

            $this->siteMapEvent->getUrlContainer()->addUrl(
                new UrlConcrete($loc, $lastModify, $changeFrequency, $priority),
                'article'
            );

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

            //article categories
            $loc = $this->urlGenerator->generate(
                'domain_article_category',
                [
                    'categorySlug' => $category->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $this->siteMapEvent->getUrlContainer()->addUrl(
                new UrlConcrete($loc),
                'article'
            );

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

    protected function addCatalogUrl($catalogLocalitySlug = null, $categorySlug = null, $subcategorySlug = null)
    {
        $loc = $this->urlGenerator->generate(
            'domain_search_catalog',
            [
                'localitySlug'    => $catalogLocalitySlug,
                'categorySlug'    => $categorySlug,
                'subcategorySlug' => $subcategorySlug,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->siteMapEvent->getUrlContainer()->addUrl(
            new UrlConcrete($loc),
            'catalog'
        );
    }
}
