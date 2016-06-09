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
        $searchManager  = $this->get('domain_site.manager.search');
        $bannerManager  = $this->get('domain_banner.manager.banner');
        $artcileManager = $this->get('domain_site.manager.search');


        $searchMenuList = $searchManager->getQuickSearchMenuLinksList();
        $banner         = $bannerManager->getBanner(TypeInterface::CODE_HOME);

        return $this->render(
            'DomainSiteBundle:Home:home.html.twig',
            array(
                'searchMenuList'    => $searchMenuList,
                'banner'            => $banner
            )
        );
    }
}
