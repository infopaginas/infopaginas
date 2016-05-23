<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\TasksManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DomainBusinessBundle:Default:index.html.twig');
    }
}
