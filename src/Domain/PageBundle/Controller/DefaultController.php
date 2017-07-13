<?php

namespace Domain\PageBundle\Controller;

use Domain\BannerBundle\Model\TypeInterface;
use Domain\SearchBundle\Model\DataType\DCDataDTO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewContactAction()
    {
        $code = $this->get('domain_page.manager.page')->getPage()::CODE_CONTACT_US;

        return $this->renderPageByCode($code);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewTermsAction()
    {
        $code = $this->get('domain_page.manager.page')->getPage()::CODE_TERMS_OF_USE;

        return $this->renderPageByCode($code);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewPrivacyAction()
    {
        $code = $this->get('domain_page.manager.page')->getPage()::CODE_PRIVACY_STATEMENT;

        return $this->renderPageByCode($code);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAdvertiseAction()
    {
        $code = $this->get('domain_page.manager.page')->getPage()::CODE_ADVERTISE;

        return $this->renderPageByCode($code);
    }

    /**
     * @param string $slug
     *
     * @return Response
     */
    private function renderPageByCode($slug)
    {
        $page = $this->get('domain_page.manager.page')->getPageByCode($slug);

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
