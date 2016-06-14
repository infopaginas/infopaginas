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
        $menuManager    = $this->get('domain_menu.manager.menu');
        $bannerManager  = $this->get('domain_banner.manager.banner');
        $articleManager = $this->get('domain_article.manager.article');
        //temporary call for article manger instead of video manager
        $videoManager   = $this->get('domain_article.manager.article');

        $articles       = $articleManager->fetchHomepageArticles();
        $videos         = $videoManager->fetchHomepageArticles();

        $menuItems      = $menuManager->fetchAll();
        $banner         = $bannerManager->getBanner(TypeInterface::CODE_HOME);

        return $this->render(
            'DomainSiteBundle:Home:home.html.twig',
            array(
                'menuItems'    => $menuItems,
                'banner'       => $banner,
                'articles'     => $articles,
                'videos'       => $videos
            )
        );
    }
}
