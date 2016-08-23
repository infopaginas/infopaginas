<?php

namespace Domain\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function viewAboutAction()
    {
        $code = $this->get('domain_page.manager.page')->getPage()::CODE_ABOUT_AS;

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

    private function renderPageByCode($slug)
    {
        $page = $this->get('domain_page.manager.page')->getPageByCode($slug);

        $params = [
            'page'      => $page,
            'seoData'   => $page,
        ];

        return $this->render('DomainPageBundle:Default:view.html.twig', $params);
    }
}
