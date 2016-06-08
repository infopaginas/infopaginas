<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
        return $this->render('DomainSiteBundle:Home:home.html.twig');
    }
}
