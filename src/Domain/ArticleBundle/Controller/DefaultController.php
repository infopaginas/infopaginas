<?php

namespace Domain\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }

    public function viewAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }
}
