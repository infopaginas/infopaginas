<?php

namespace Domain\SiteBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\CustomVariable;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\SiteBundle\Form\Type\RegistrationType;
use Domain\SiteBundle\Form\Type\LoginType;
use Domain\SiteBundle\Form\Type\ResetPasswordRequestType;
use Domain\SiteBundle\Form\Type\ResetPasswordType;
use Domain\SiteBundle\Utils\Helpers\GoogleAnalyticsHelper;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Domain\BannerBundle\Model\TypeInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

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
        $videoManager   = $this->get('domain_business.video');

        $articles       = $articleManager->fetchHomepageArticles();
        $videos         = $videoManager->fetchHomepageVideos();

        $menuItems      = $menuManager->fetchAll();

        $bannerFactory  = $this->get('domain_banner.factory.banner');
        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_SERP_BANNER,
            TypeInterface::CODE_PORTAL_LEFT,
            TypeInterface::CODE_PORTAL_RIGHT,
            TypeInterface::CODE_PORTAL_LEFT_MOBILE,
            TypeInterface::CODE_PORTAL_RIGHT_MOBILE,
        ));

        $userRoles = $this->get('security.token_storage')->getToken()->getRoles();
        $roleForGA = GoogleAnalyticsHelper::getUserRoleForAnalytics($userRoles);

        $this->get('google.analytics')->addCustomVariable(new CustomVariable('default', 'dimension1', $roleForGA));

        return $this->render(
            'DomainSiteBundle:Home:home.html.twig',
            [
                'menuItems'                => $menuItems,
                'bannerFactory'            => $bannerFactory,
                'articles'                 => $articles,
                'videos'                   => $videos,
                'locale'                   => $locale,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authModalAction()
    {
        $loginForm                = $this->createForm(new LoginType());
        $registrationForm         = $this->createForm(new RegistrationType());
        $resetPasswordRequestForm = $this->createForm(new ResetPasswordRequestType());
        $resetPasswordForm        = $this->createForm(new ResetPasswordType());

        return $this->render(
            'DomainSiteBundle:Home:auth_modal.html.twig',
            [
                'loginForm'                => $loginForm->createView(),
                'registrationForm'         => $registrationForm->createView(),
                'resetPasswordRequestForm' => $resetPasswordRequestForm->createView(),
                'resetPasswordForm'        => $resetPasswordForm->createView(),
            ]
        );
    }
}
