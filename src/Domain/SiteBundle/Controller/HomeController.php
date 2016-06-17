<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Domain\BannerBundle\Model\TypeInterface;

/**
 * Class HomeController
 * @package Domain\SiteBundle\Controller
 */
class HomeController extends Controller
{
    /**
     * Temp actions and templates
     */
    public function indexAction(Request $request)
    {
        $locale         = $request->getLocale();

        $menuManager    = $this->get('domain_menu.manager.menu');
        $bannerFactory  = $this->get('domain_banner.factory.banner'); // Maybe need to load via factory, not manager
        $articleManager = $this->get('domain_article.manager.article');
        //temporary call for article manger instead of video manager
        $videoManager   = $this->get('domain_article.manager.article');

        $articles       = $articleManager->fetchHomepageArticles();
        $videos         = $videoManager->fetchHomepageArticles();

        $menuItems      = $menuManager->fetchAll();
        $banner         = $bannerFactory->get(TypeInterface::CODE_PORTAL_LEADERBOARD);
        $bannerBottom   = $bannerFactory->get(TypeInterface::CODE_PORTAL);

        return $this->render(
            'DomainSiteBundle:Home:home.html.twig',
            array(
                'menuItems'    => $menuItems,
                'banner'       => $banner,
                'bannerBottom' => $bannerBottom,
                'articles'     => $articles,
                'videos'       => $videos,
                'locale'       => $locale
            )
        );
    }
}
