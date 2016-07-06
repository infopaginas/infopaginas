<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Form\Handler\BusinessProfileFormHandler;
use Domain\BusinessBundle\Form\Handler\FreeBusinessProfileFormHandler;
use Domain\BusinessBundle\Manager\BusinessProfilesManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE = 'Business Profile Request send. Please wait for approval';

    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    const INTERNAL_SERVER_ERROR_STATUS_CODE = 500;

    //add create action
    //use form extending
    //translatable entity
    //remove media tab
    //remove some checkboxes

    public function createAction()
    {
        $freeBusinessProfileForm = $this->getFreeBusinessProfileForm();

        return $this->render('DomainBusinessBundle:Profile:create.html.twig', [
            'businessProfileForm' => $freeBusinessProfileForm->createView(),
        ]);
    }

    public function editAction(Request $request, int $id)
    {
        $businessProfile = $this->getBusinessProfilesManager()->find($id);

        $businessProfileForm = $this->getBusinessProfileForm($businessProfile);

        return $this->render('DomainBusinessBundle:Profile:create.html.twig', [
            'businessProfileForm' => $businessProfileForm->createView(),
        ]);
    }

    public function translateBusinessFormAction(Request $request, int $businessProfileId, string $locale)
    {
        $businessProfile = $this->getBusinessProfilesManager()->find($businessProfileId, $locale);

        $businessProfileForm = $this->getBusinessProfileForm($businessProfile);

        return $this->render('DomainBusinessBundle:Profile/blocks:edit_form.html.twig', [
            'businessProfileForm' => $businessProfileForm->createView(),
        ]);
    }

    public function saveAction()
    {
        $formHandler = $this->getFreeBusinessProfileFormHandler();

        if ($formHandler->process()) {
            return $this->getSuccessResponse(self::SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    private function getBusinessProfilesManager() : BusinessProfilesManager
    {
        return $this->get('domain_business.manager.business_profiles');
    }

    private function getFreeBusinessProfileForm() : FormInterface
    {
        return $this->get('domain_business.form.business_profile.free');
    }

    private function getFreeBusinessProfileFormHandler() : FreeBusinessProfileFormHandler
    {
        return $this->get('domain_business.form.handler.business_profile.free');
    }

    private function getBusinessProfileForm($businessProfile = false) : FormInterface
    {
        //todo: check subscription here
        if (true) {
            $form = $this->get('domain_business.form.business_profile.free');
        } else {
            $form = $this->get('domain_business.form.business_profile');
        }

        if ($businessProfile === null) {
            return $form;
        }

        $form->setData($businessProfile);

        return $form;
    }

    private function getBusinessProfileFormHandler() : BusinessProfileFormHandler
    {
        return $this->get('domain_business.form.handler.business_profile');
    }
}
