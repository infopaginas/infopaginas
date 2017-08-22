<?php

namespace Domain\SiteBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\CustomVariable;
use Domain\BusinessBundle\Manager\LandingPageShortCutManager;
use Domain\PageBundle\Model\PageInterface;
use Domain\SiteBundle\Form\Type\RegistrationType;
use Domain\SiteBundle\Form\Type\LoginType;
use Domain\SiteBundle\Form\Type\ResetPasswordRequestType;
use Domain\SiteBundle\Form\Type\ResetPasswordType;
use Domain\SiteBundle\Utils\Helpers\GoogleAnalyticsHelper;
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

        $articleManager = $this->get('domain_article.manager.article');
        $articles       = $articleManager->fetchHomepageArticles($locale);

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_HOME_VERTICAL,
                TypeInterface::CODE_LANDING_PAGE_RIGHT,
            ]
        );

        $userRoles = $this->get('security.token_storage')->getToken()->getRoles();
        $roleForGA = GoogleAnalyticsHelper::getUserRoleForAnalytics($userRoles);

        $this->get('google.analytics')->addCustomVariable(new CustomVariable('default', 'dimension1', $roleForGA));
        $schema = $articleManager->buildArticlesSchema($articles);

        $landingPage = $this->get('domain_page.manager.page')->getPageByCode(PageInterface::CODE_LANDING);

        return $this->render(
            ':redesign:homepage.html.twig',
            [
                'banners'       => $banners,
                'articles'      => $articles,
                'locale'        => $locale,
                'schemaJsonLD'  => $schema,
                'hideHeaderSearch' => true,
                'landingPage'   => $landingPage,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function popularMenuItemsAction(Request $request)
    {
        $type  = $request->get('type', LandingPageShortCutManager::SHORT_CUT_ITEMS_LANDING);
        $title = $request->get('title', '');

        $shortCutManager = $this->get('domain_business.manager.landing_page_short_cut_manager');
        $shortCutItems   = $shortCutManager->getLandingPageShortCutItems($request->getLocale());

        return $this->render(
            ':redesign/blocks:popular_menu_items.html.twig',
            [
                'shortCutItems' => $shortCutItems,
                'type'          => $type,
                'title'         => $title,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authModalRedesignAction()
    {
        $loginForm                = $this->createForm(new LoginType());
        $registrationForm         = $this->createForm(new RegistrationType());
        $resetPasswordRequestForm = $this->createForm(new ResetPasswordRequestType());
        $resetPasswordForm        = $this->createForm(new ResetPasswordType());

        return $this->render(
            ':redesign/blocks:auth_modal.html.twig',
            [
                'loginForm'                => $loginForm->createView(),
                'registrationForm'         => $registrationForm->createView(),
                'resetPasswordRequestForm' => $resetPasswordRequestForm->createView(),
                'resetPasswordForm'        => $resetPasswordForm->createView(),
            ]
        );
    }
}
