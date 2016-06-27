<?php

namespace Domain\SiteBundle\Controller;

use Domain\SiteBundle\Form\Handler\RegistrationFormHandler;
use Domain\SiteBundle\Form\Handler\ResetPasswordRequestFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * Class AuthController
 * @package Domain\SiteBundle\Controller
 */
class AuthController extends Controller
{
    const ERROR_VALIDATION_FAILURE = 'Validation failure.';

    const SUCCESS_REGISTERED_MESSAGE = 'Successfully registered. Please login with your credentials.';

    const SUCCESS_RESET_LINK_SENT_MESSAGE = 'Reset password link sent to your email. Please check it.';

    const SUCCESS_RESET_PASSWORD_MESSAGE = 'Successfully updated password. You can login with new credentials now.';

    /**
     * @return JsonResponse
     */
    public function registrationAction() : JsonResponse
    {
        $formHandler = $this->getRegistrationFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_REGISTERED_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return JsonResponse
     */
    public function sendPasswordResetLinkAction() : JsonResponse
    {
        $formHandler = $this->getResetPasswordRequestFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_RESET_LINK_SENT_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    public function resetPasswordAction(Request $request)
    {
        $formHandler = $this->getResetPasswordFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_RESET_PASSWORD_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return DataCollectorTranslator
     */
    private function getTranslator() : DataCollectorTranslator
    {
        return $this->get('translator');
    }

    private function getResetPasswordFormHandler()
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
