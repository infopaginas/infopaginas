<?php

namespace Domain\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DomainMenuBundle:Default:index.html.twig');
    }
}
