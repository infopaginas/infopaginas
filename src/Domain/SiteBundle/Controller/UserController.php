<?php

namespace Domain\SiteBundle\Controller;

use Domain\BusinessBundle\Form\Type\BusinessCloseRequestType;
use Domain\BusinessBundle\Form\Type\BusinessUpgradeRequestType;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Form\Handler\PasswordUpdateFormHandler;
use Domain\SiteBundle\Form\Handler\UserProfileFormHandler;
use FOS\UserBundle\Model\UserInterface;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationManager;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction()
    {
        $profileForm        = $this->getUserProfileForm();
        $passwordUpdateForm = $this->getPasswordUpdateForm();
        $businessOverviewReportManager = $this->getBusinessOverviewReportManager();

        $user = $this->getCurrentUser();

        $usersManager = $this->getUsersManager();

        $userBusinessProfiles = $usersManager->getUserBusinessProfiles($user);
        $userBusinessProfilesIds = BusinessProfileUtil::extractEntitiesId($userBusinessProfiles);

        $actions = BusinessOverviewModel::getActionsUserData();

        $summaryData = $businessOverviewReportManager->getSummaryByActionData(
            $userBusinessProfilesIds,
            array_keys($actions),
            DatesUtil::getLastMonth()
        );

        $closeBusinessProfileForm = $this->createForm(BusinessCloseRequestType::class);
        $upgradeBusinessProfileForm = $this->createForm(BusinessUpgradeRequestType::class);

        return $this->render(':redesign:user-profile.html.twig', [
            'profileForm'                => $profileForm->createView(),
            'closeBusinessProfileForm'   => $closeBusinessProfileForm->createView(),
            'upgradeBusinessProfileForm' => $upgradeBusinessProfileForm->createView(),
            'passwordUpdateForm'         => $passwordUpdateForm->createView(),
            'userBusinessProfiles'       => $userBusinessProfiles,
            'summaryData'                => $summaryData,
            'actions'                    => $actions,
            'tooltips'                   => BusinessOverviewModel::getActionTooltip(),
        ]);
    }

    /**
     * @return BusinessOverviewReportManager
     */
    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->get('domain_report.manager.business_overview_report_manager');
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
     * @return JsonResponse
     */
    public function processUpgradeAction()
    {
        $formHandler = $this->getBusinessProfileUpgradeRequestFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse('success');
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

    /**
     * @return \Domain\BusinessBundle\Form\Handler\BusinessUpgradeRequestFormHandler
     */
    private function getBusinessProfileUpgradeRequestFormHandler()
    {
        return $this->get('domain_business.form.handler.business_upgrade_request');
    }
}
