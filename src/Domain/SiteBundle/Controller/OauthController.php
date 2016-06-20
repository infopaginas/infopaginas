<?php

namespace Domain\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OauthController extends Controller
{
    public function indexAction()
    {
        /*$userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findOneBy(['email' => 'xedinaska@gmail.com']);
        $user->setPlainPassword('123123');

        $userManager->updateUser($user, true);*/

        return $this->render('DomainSiteBundle:Oauth:login.html.twig');
    }
}
