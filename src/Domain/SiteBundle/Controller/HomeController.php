<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
    public function indexAction()
    {
        return $this->render('DomainSiteBundle:Home:index.html.twig');
    }

    /**
     * Temp actions and templates
     */
    public function homeAction()
    {
        $menuManager    = $this->get('domain_menu.manager.menu');
        $bannerManager  = $this->get('domain_banner.manager.banner');
        $artcileManager = $this->get('domain_site.manager.search');

        $menuItems      = $menuManager->fetchAll();
        $banner         = $bannerManager->getBanner(TypeInterface::CODE_HOME);
// dump($menuItems); die;
        return $this->render(
            'DomainSiteBundle:Home:home.html.twig',
            array(
                'menuItems'    => $menuItems,
                'banner'       => $banner
            )
        );
    }
}
