<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Handler\FreeBusinessProfileFormHandler;
use Domain\BusinessBundle\Form\Type\FreeBusinessProfileFormType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfileController
 * @package Domain\BusinessBundle\Controller
 */
class ProfileController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE = 'Business Profile Request send. Please wait for approval';

    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    const INTERNAL_SERVER_ERROR_STATUS_CODE = 500;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $businessProfileForm = $this->getBusinessProfileForm();

        return $this->render('DomainBusinessBundle:Profile:edit.html.twig', [
            'businessProfileForm' => $businessProfileForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $locale = $request->request->get('locale', BusinessProfile::DEFAULT_LOCALE);

        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfilesManager()->find($id, $locale);

        $businessProfileForm = $this->getBusinessProfileForm($businessProfile);

        //return form only for AJAX requests
        if (!$request->isXmlHttpRequest()) {
            $template = 'DomainBusinessBundle:Profile:edit.html.twig';
        } else {
            $template = 'DomainBusinessBundle:Profile/blocks:edit_form.html.twig';
        }

        return $this->render($template, [
            'businessProfileForm' => $businessProfileForm->createView(),
            'businessProfile'     => $businessProfile,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function saveAction()
    {
        $formHandler = $this->getBusinessProfileFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE);
            }
        } catch (Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], 500);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @return FreeBusinessProfileFormHandler
     */
    private function getBusinessProfileFormHandler() : FreeBusinessProfileFormHandler
    {
        return $this->get('domain_business.form.handler.business_profile.free');
    }

    /**
     * @param bool $businessProfile
     * @return FormInterface
     */
    private function getBusinessProfileForm($businessProfile = false) : FormInterface
    {
        if ($businessProfile === false) {
            $businessProfile = $this->getBusinessProfilesManager()->createProfile();
        }

        return $this->createForm(new FreeBusinessProfileFormType(), $businessProfile);
    }
}
