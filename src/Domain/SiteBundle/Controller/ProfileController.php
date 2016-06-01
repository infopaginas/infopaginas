<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ProfileController
 * @package Domain\SiteBundle\Controller
 */
class ProfileController extends Controller
{
    /**
     * Main profile page
     */
    public function indexAction()
    {
        return $this->render('DomainSiteBundle:Home:profile.html.twig');
    }
}
