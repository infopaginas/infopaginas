<?php

namespace Domain\SiteBundle\Controller;

use Domain\SiteBundle\Form\Handler\RegistrationFormHandler;
use Domain\SiteBundle\Form\Handler\ResetPasswordFormHandler;
use Domain\SiteBundle\Form\Handler\ResetPasswordRequestFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AuthController
 * @package Domain\SiteBundle\Controller
 */
class AuthController extends Controller
{
    const ERROR_VALIDATION_FAILURE = 'user.auth.validation_failure';

    const SUCCESS_REGISTERED_MESSAGE = 'user.auth.registration_success';

    const SUCCESS_RESET_LINK_SENT_MESSAGE = 'user.auth.reset_link_sent';

    const SUCCESS_RESET_PASSWORD_MESSAGE = 'user.auth.password_updated';

    /**
     * @return JsonResponse
     */
    public function registrationAction() : JsonResponse
    {
        $formHandler = $this->getRegistrationFormHandler();
        $translator  = $this->getTranslator();

        try {
            if ($formHandler->process()) {
                $message = $translator->trans(self::SUCCESS_REGISTERED_MESSAGE);

                return $this->getSuccessResponse($message);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        $message = $translator->trans(self::ERROR_VALIDATION_FAILURE);

        return $this->getFailureResponse($message, $formHandler->getErrors());
    }

    /**
     * @return JsonResponse
     */
    public function sendPasswordResetLinkAction() : JsonResponse
    {
        $formHandler = $this->getResetPasswordRequestFormHandler();
        $translator  = $this->getTranslator();

        try {
            if ($formHandler->process()) {
                $message = $translator->trans(self::SUCCESS_RESET_LINK_SENT_MESSAGE);

                return $this->getSuccessResponse($message);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        $message = $translator->trans(self::ERROR_VALIDATION_FAILURE);

        return $this->getFailureResponse($message, $formHandler->getErrors());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordAction(Request $request) : JsonResponse
    {
        $formHandler = $this->getResetPasswordFormHandler();
        $translator  = $this->getTranslator();

        try {
            if ($formHandler->process()) {
                $message = $translator->trans(self::SUCCESS_RESET_LINK_SENT_MESSAGE);

                return $this->getSuccessResponse($message);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        $message = $translator->trans(self::ERROR_VALIDATION_FAILURE);

        return $this->getFailureResponse($message, $formHandler->getErrors());
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator() : TranslatorInterface
    {
        return $this->get('translator');
    }

    /**
     * @return ResetPasswordFormHandler
     */
    private function getResetPasswordFormHandler() : ResetPasswordFormHandler
    {
        return $this->get('domain_site.reset_password.form.handler');
    }

    /**
     * @return ResetPasswordRequestFormHandler
     */
    private function getResetPasswordRequestFormHandler() : ResetPasswordRequestFormHandler
    {
        return $this->get('domain_site.reset_password_request.form.handler');
    }

    /**
     * @return RegistrationFormHandler
     */
    private function getRegistrationFormHandler() : RegistrationFormHandler
    {
        return $this->get('domain_site.registration.form.handler');
    }

    /**
     * @param string $message
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
