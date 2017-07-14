<?php

namespace Domain\PageBundle\Controller;

use Domain\BannerBundle\Model\TypeInterface;
use Domain\PageBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewContactAction()
    {
        return $this->renderPageByCode(Page::CODE_CONTACT_US);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewTermsAction()
    {
        return $this->renderPageByCode(Page::CODE_TERMS_OF_USE);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewPrivacyAction()
    {
        return $this->renderPageByCode(Page::CODE_PRIVACY_STATEMENT);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAdvertiseAction()
    {
        return $this->renderPageByCode(Page::CODE_ADVERTISE);
    }

    /**
     * @param int $code
     *
     * @return Response
     */
    private function renderPageByCode($code)
    {
        $page = $this->get('domain_page.manager.page')->getPageByCode($code);

        $bannerFactory = $this->get('domain_banner.factory.banner');

        $bannerFactory->prepareBanners(
            [
                TypeInterface::CODE_PORTAL_RIGHT,
                TypeInterface::CODE_STATIC_BOTTOM,
            ]
        );

        $params = [
            'page'          => $page,
            'seoData'       => $page,
            'bannerFactory' => $bannerFactory,
        ];

        return $this->render(':redesign:static-page-view.html.twig', $params);
    }
}
