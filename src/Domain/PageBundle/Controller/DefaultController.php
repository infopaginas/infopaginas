<?php

namespace Domain\PageBundle\Controller;

use Domain\BannerBundle\Model\TypeInterface;
use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Form\Handler\FeedbackFormHandler;
use Domain\PageBundle\Form\Type\FeedbackFormType;
use Oxa\ConfigBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class DefaultController extends Controller
{
    const ERROR_VALIDATION_FAILURE = 'contact.form.validation_failure';
    const SUCCESS_FEEDBACK_SEND    = 'contact.form.send_success';

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
        $pageManager = $this->get('domain_page.manager.page');
        $page = $pageManager->getPageByCode($code);

        if ($page->getRedirectUrl()) {
            $redirectUrl = filter_var($page->getRedirectUrl(), FILTER_VALIDATE_URL);

            if ($redirectUrl) {
                return $this->redirect($redirectUrl);
            }
        }

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_PORTAL_RIGHT,
                TypeInterface::CODE_STATIC_BOTTOM,
            ]
        );

        if (Page::getShowContactForm($code)) {
            $contactForm     = $this->createForm(new FeedbackFormType(), null, ['isReportProblem' => false]);
            $contactFormView = $contactForm->createView();
            $instantEmail    = $this->getConfigManager()->getInstantEmail();
        } else {
            $contactFormView = null;
            $instantEmail    = null;
        }

        $params = [
            'page'      => $page,
            'seoData'   => $pageManager->getPageSeoData($page),
            'banners'   => $banners,
            'contactForm'  => $contactFormView,
            'instantEmail' => $instantEmail,
        ];

        return $this->render(':redesign:static-page-view.html.twig', $params);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function sendFeedbackAction(Request $request) : JsonResponse
    {
        $formHandler = $this->getFeedbackFormHandler();
        $translator  = $this->getTranslator();
        $user        = $this->getUser();

        $formHandler->setUser($user);

        try {
            if ($formHandler->process()) {
                $message = $translator->trans(self::SUCCESS_FEEDBACK_SEND);

                return $this->getSuccessResponse($message);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        $message = $translator->trans(self::ERROR_VALIDATION_FAILURE);

        return $this->getFailureResponse($message, $formHandler->getErrors());
    }

    /**
     * @return FeedbackFormHandler
     */
    private function getFeedbackFormHandler() : FeedbackFormHandler
    {
        return $this->get('domain_page.feedback.form.handler');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator() : TranslatorInterface
    {
        return $this->get('translator');
    }

    /**
     * @return Config
     */
    private function getConfigManager() : Config
    {
        return $this->get('oxa_config');
    }

    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    private function getSuccessResponse(string $message) : JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $this->getTranslator()->trans($message),
        ]);
    }

    /**
     * @param string $message
     * @param array $errors
     *
     * @return JsonResponse
     */
    private function getFailureResponse(string $message = '', array $errors = []) : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $this->getTranslator()->trans($message),
            'errors'  => $errors,
        ]);
    }
}
