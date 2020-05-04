<?php

namespace Domain\SiteBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\CustomVariable;
use Domain\BusinessBundle\Manager\HomepageCarouselManager;
use Domain\BusinessBundle\Manager\LandingPageShortCutManager;
use Domain\PageBundle\Model\PageInterface;
use Domain\SearchBundle\Util\CacheUtil;
use Domain\SiteBundle\Form\Type\RegistrationType;
use Domain\SiteBundle\Form\Type\LoginType;
use Domain\SiteBundle\Form\Type\ResetPasswordRequestType;
use Domain\SiteBundle\Form\Type\ResetPasswordType;
use Domain\SiteBundle\Utils\Helpers\GoogleAnalyticsHelper;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Domain\BannerBundle\Model\TypeInterface;
use Symfony\Component\HttpFoundation\Response;

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
        $locale         = LocaleHelper::getLocale($request->getLocale());

        $articleManager = $this->get('domain_article.manager.article');
        $articles       = $articleManager->fetchHomepageArticles($locale);

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_HOME_VERTICAL,
                TypeInterface::CODE_LANDING_PAGE_RIGHT_LARGE,
            ]
        );

        $userRoles = $this->get('security.token_storage')->getToken()->getRoles();
        $roleForGA = GoogleAnalyticsHelper::getUserRoleForAnalytics($userRoles);

        $this->get('google.analytics')->addCustomVariable(new CustomVariable('default', 'dimension1', $roleForGA));
        $schema = $articleManager->buildArticlesSchema($articles);

        $landingPage = $this->get('domain_page.manager.page')->getPageByCode(PageInterface::CODE_LANDING);
        $seoData     = $this->get('domain_page.manager.page')->getPageSeoData($landingPage);

        $homepageCarouselManager = $this->getHomepageCarouselManager();
        $carouselBusinesses = $homepageCarouselManager->getCarouselBusinessesSortedByRandom();
        $showCarousel = $homepageCarouselManager->isShowCarousel($carouselBusinesses);

        return $this->render(
            ':redesign:homepage.html.twig',
            [
                'banners'            => $banners,
                'articles'           => $articles,
                'locale'             => $locale,
                'schemaJsonLD'       => $schema,
                'hideHeaderSearch'   => true,
                'landingPage'        => $landingPage,
                'seoData'            => $seoData,
                'page'               => $landingPage,
                'carouselBusinesses' => $carouselBusinesses,
                'showCarousel'       => $showCarousel,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function popularMenuItemsAction(Request $request)
    {
        $type  = $request->get('type', LandingPageShortCutManager::SHORT_CUT_ITEMS_LANDING);
        $title = $request->get('title', '');
        $locale = LocaleHelper::getLocale($request->getLocale());

        $memcached = $this->get('app.cache.memcached');

        $response = null;
        $keyIncrement = $memcached->fetch(CacheUtil::PREFIX_HOMEPAGE_SHORTCUT);
        if ($keyIncrement !== false) {
            $response = $memcached->fetch(
                CacheUtil::PREFIX_HOMEPAGE_SHORTCUT . $keyIncrement . $type . $title . $locale
            );
        } else {
            $keyIncrement = 0;
            $memcached->save(CacheUtil::PREFIX_HOMEPAGE_SHORTCUT, $keyIncrement);
        }

        if (!$response) {
            $shortCutManager = $this->get('domain_business.manager.landing_page_short_cut_manager');
            $shortCutItems = $shortCutManager->getLandingPageShortCutItems($locale);

            $response = $this->render(
                ':redesign/blocks:popular_menu_items.html.twig',
                [
                    'shortCutItems' => $shortCutItems,
                    'type' => $type,
                    'title' => $title,
                ]
            );
            $memcached->save(
                CacheUtil::PREFIX_HOMEPAGE_SHORTCUT . $keyIncrement . $type . $title . $locale,
                $response,
                CacheUtil::HOMEPAGE_SHORTCUT_CACHE_LIFETIME
            );
        }

        return $response;
    }

    /**
     * @return Response
     */
    public function authModalRedesignAction()
    {
        $loginForm                = $this->createForm(LoginType::class);
        $registrationForm         = $this->createForm(RegistrationType::class);
        $resetPasswordRequestForm = $this->createForm(ResetPasswordRequestType::class);
        $resetPasswordForm        = $this->createForm(ResetPasswordType::class);

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

    /**
     * @return HomepageCarouselManager
     */
    protected function getHomepageCarouselManager() : HomepageCarouselManager
    {
        return $this->get('domain_business.manager.homepage_carousel_manager');
    }
}
