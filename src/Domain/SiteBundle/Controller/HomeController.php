<?php

namespace Domain\SiteBundle\Controller;

use Domain\SiteBundle\Form\Type\RegistrationType;
use Domain\SiteBundle\Form\Type\LoginType;
use Domain\SiteBundle\Form\Type\ResetPasswordRequestType;
use Domain\SiteBundle\Form\Type\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction(Request $request)
    {
        $locale         = $request->getLocale();

        $menuManager    = $this->get('domain_menu.manager.menu');
        $articleManager = $this->get('domain_article.manager.article');
        //temporary call for article manger instead of video manager
        $videoManager   = $this->get('domain_article.manager.article');

        $articles       = $articleManager->fetchHomepageArticles();
        $videos         = $videoManager->fetchHomepageArticles();

        $menuItems      = $menuManager->fetchAll();

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_PORTAL_LEADERBOARD,
            TypeInterface::CODE_PORTAL_LEFT,
            TypeInterface::CODE_PORTAL_RIGHT,
        ));

        $loginForm                = $this->createForm(new LoginType());
        $registrationForm         = $this->createForm(new RegistrationType());
        $resetPasswordRequestForm = $this->createForm(new ResetPasswordRequestType());
        $resetPasswordForm        = $this->createForm(new ResetPasswordType());

        return $this->render(
            'DomainSiteBundle:Home:home.html.twig',
            array(
                'menuItems'                => $menuItems,
                'bannerFactory'            => $bannerFactory,
                'articles'                 => $articles,
                'videos'                   => $videos,
                'locale'                   => $locale,
                'loginForm'                => $loginForm->createView(),
                'registrationForm'         => $registrationForm->createView(),
                'resetPasswordRequestForm' => $resetPasswordRequestForm->createView(),
                'resetPasswordForm'        => $resetPasswordForm->createView(),
            )
        );
    }
}
