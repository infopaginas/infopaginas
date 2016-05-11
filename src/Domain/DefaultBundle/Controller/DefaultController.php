<?php

namespace Domain\DefaultBundle\Controller;

use Oxa\Sonata\UserBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $group = $this->getDoctrine()
            ->getRepository('OxaSonataUserBundle:Group')
            ->findOneBy(['code'=>Group::CODE_ADMINISTRATOR]);

//        $group
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }
}
