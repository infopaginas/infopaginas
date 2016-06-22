<?php

namespace Domain\SiteBundle\Controller;

use Domain\SiteBundle\Form\Handler\RegistrationFormHandler;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AuthController
 * @package Domain\SiteBundle\Controller
 */
class AuthController extends Controller
{
    const ERROR_NOT_HANDLED_MESSAGE = 'Undefined error. Please contact support team';
    const ERROR_VALIDATION_FAILURE = 'Validation failure';

    const SUCCESS_REGISTERED_MESSAGE = 'Successfully registered. Please login with your credentials';

    /**
     * @return JsonResponse
     */
    public function registrationAction() : JsonResponse
    {
        $formHandler = $this->getRegistrationFormHandler();

        try {
            $form = $this->getRegistrationForm();

            $isProcessed = $formHandler->process();

            if ($isProcessed) {
                return $this->getSuccessResponse();
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }



    /**
     * @return RegistrationFormHandler
     */
    private function getRegistrationFormHandler() : RegistrationFormHandler
    {
        return $this->get('domain_site.registration.form.handler');
    }

    /**
     * @return Form
     */
    private function getRegistrationForm() : Form
    {
        return $this->get('domain_site.registration.form');
    }

    /**
     * @return JsonResponse
     */
    private function getSuccessResponse() : JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => self::SUCCESS_REGISTERED_MESSAGE,
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
