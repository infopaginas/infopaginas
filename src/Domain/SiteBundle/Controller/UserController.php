<?php

namespace Domain\SiteBundle\Controller;

use Domain\SiteBundle\Form\Handler\PasswordUpdateFormHandler;
use Domain\SiteBundle\Form\Handler\UserProfileFormHandler;
use FOS\UserBundle\Model\UserInterface;
use Oxa\Sonata\UserBundle\Manager\UsersManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserController
 * @package Domain\SiteBundle\Controller
 */
class UserController extends Controller
{
    const SUCCESS_PROFILE_UPDATE_MESSAGE = 'Successfully saved profile data';

    const SUCCESS_PASSWORD_UPDATE_MESSAGE = 'Successfully updated password';

    const ERROR_VALIDATION_FAILURE = 'Validation failure';

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(Request $request)
    {
        $profileForm        = $this->getUserProfileForm();
        $passwordUpdateForm = $this->getPasswordUpdateForm();

        $user = $this->getCurrentUser();

        $usersManager = $this->getUsersManager();

        $userBusinessProfiles = $usersManager->getUserBusinessProfiles($user);
        $userReviews = $usersManager->getUserReviews($user);

        return $this->render(':redesign:user-profile.html.twig', [
            'profileForm'          => $profileForm->createView(),
            'passwordUpdateForm'   => $passwordUpdateForm->createView(),
            'userBusinessProfiles' => $userBusinessProfiles,
            'userReviews'          => $userReviews,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function saveProfileAction()
    {
        $formHandler = $this->getUserProfileFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_PROFILE_UPDATE_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return JsonResponse
     */
    public function passwordUpdateAction()
    {
        $formHandler = $this->getPasswordUpdateFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_PASSWORD_UPDATE_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage());
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return UserInterface
     */
    private function getCurrentUser() : UserInterface
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * @return UsersManager
     */
    private function getUsersManager() : UsersManager
    {
        return $this->get('oxa.manager.users');
    }

    /**
     * @return PasswordUpdateFormHandler
     */
    private function getPasswordUpdateFormHandler() : PasswordUpdateFormHandler
    {
        return $this->get('domain_site.user_password_update.form.handler');
    }

    /**
     * @return FormInterface
     */
    private function getPasswordUpdateForm() : FormInterface
    {
        return $this->get('domain_site.user_password_update.form');
    }

    /**
     * @return UserProfileFormHandler
     */
    private function getUserProfileFormHandler() : UserProfileFormHandler
    {
        return $this->get('domain_site.user_profile.form.handler');
    }

    /**
     * @return FormInterface
     */
    private function getUserProfileForm() : FormInterface
    {
        $form = $this->get('domain_site.user_profile.form');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form->setData($user);

        return $form;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator() : TranslatorInterface
    {
        return $this->get('translator');
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
