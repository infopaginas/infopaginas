<?php

namespace Domain\SiteBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
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
        $this->addBusinessProfiles($event);
        $this->addArticleList($event);

        //todo article catalog
        //todo business catalog
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    protected function addBusinessProfiles(SitemapPopulateEvent $event)
    {
        $businessProfiles = $this->manager->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getActiveBusinessProfilesIterator();

        foreach ($businessProfiles as $row) {
            /* @var $businessProfile \Domain\BusinessBundle\Entity\BusinessProfile */
            $businessProfile = current($row);

            $loc = $this->urlGenerator->generate(
                'domain_business_profile_view',
                [
                    'citySlug' => $businessProfile->getCitySlug(),
                    'slug'     => $businessProfile->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $lastModify      = $businessProfile->getUpdatedAt();
            $changeFrequency = UrlConcrete::CHANGEFREQ_DAILY;
            $priority        = 0.7;

            $event->getUrlContainer()->addUrl(
                new UrlConcrete($loc, $lastModify, $changeFrequency, $priority),
                'businessProfiles'
            );

            $this->manager->detach($row[0]);
        }
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    protected function addArticleList(SitemapPopulateEvent $event)
    {
        //todo article may became inactive
        //todo direct link is still available

        $articles = $this->manager->getRepository('DomainArticleBundle:Article')
            ->getActiveArticlesIterator();

        foreach ($articles as $row) {
            /* @var $article \Domain\ArticleBundle\Entity\Article */
            $article = current($row);

            $loc = $this->urlGenerator->generate(
                'domain_article_view',
                [
                    'slug' => $article->getSlug(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $lastModify      = $article->getUpdatedAt();
            $changeFrequency = UrlConcrete::CHANGEFREQ_DAILY;
            $priority        = 0.7;

            $event->getUrlContainer()->addUrl(
                new UrlConcrete($loc, $lastModify, $changeFrequency, $priority),
                'article'
            );

            $this->manager->detach($row[0]);
        }
    }
}
