<?php

namespace Domain\SiteBundle\Controller;

use Domain\SiteBundle\Form\Handler\RegistrationFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * Class AuthController
 * @package Domain\SiteBundle\Controller
 */
class AuthController extends Controller
{
    const ERROR_VALIDATION_FAILURE = 'Validation failure';

    const SUCCESS_REGISTERED_MESSAGE = 'Successfully registered. Please login with your credentials';

    /**
     * @return JsonResponse
     */
    public function registrationAction() : JsonResponse
    {
        $formHandler = $this->getRegistrationFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse();
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(
            $this->getTranslator()->trans(self::ERROR_VALIDATION_FAILURE),
            $formHandler->getErrors()
        );
    }

    private function getTranslator() : DataCollectorTranslator
    {
        return $this->get('translator');
    }

    /**
     * @return RegistrationFormHandler
     */
    private function getRegistrationFormHandler() : RegistrationFormHandler
    {
        return $this->get('domain_site.registration.form.handler');
    }

    /**
     * @return JsonResponse
     */
    private function getSuccessResponse() : JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $this->getTranslator()->trans(self::SUCCESS_REGISTERED_MESSAGE),
        ]);
    }

    /**
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    private function getFailureResponse($message = '', $errors = []) : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ]);
    }
}
