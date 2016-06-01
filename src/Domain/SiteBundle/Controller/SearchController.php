<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class HomeController
 * @package Domain\SiteBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * Main Search page
     */
    public function indexAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }
}
