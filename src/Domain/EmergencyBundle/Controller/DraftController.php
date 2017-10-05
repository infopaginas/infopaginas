<?php

namespace Domain\EmergencyBundle\Controller;

use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Domain\PageBundle\Model\PageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Domain\EmergencyBundle\Manager\EmergencyManager;
use Domain\EmergencyBundle\Form\Handler\EmergencyDraftBusinessFormHandler;

/**
 * Class DraftController
 * @package Domain\EmergencyBundle\Controller
 */
class DraftController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_DRAFT_CREATED_MESSAGE = 'emergency.business_draft.created';
    const ERROR_VALIDATION_FAILURE      = 'emergency.business_draft.invalid';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $emergencyManger = $this->getEmergencyManager();

        if (!$emergencyManger->getEmergencyFeatureEnabled()) {
            throw $this->createNotFoundException();
        }

        $pageManager = $this->get('domain_page.manager.page');
        $emergencyPage = $pageManager->getPageByCode(PageInterface::CODE_EMERGENCY);

        if ($emergencyPage->getUseActionLink() and $emergencyPage->getActionLink()) {
            return $this->redirect($emergencyPage->getActionLink());
        }

        $businessDraftForm = $this->getBusinessDraftForm();

        return $this->render(
            ':redesign:emergency-business-draft-create.html.twig',
            [
                'businessForm' => $businessDraftForm->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request) : JsonResponse
    {
        $emergencyManger = $this->getEmergencyManager();

        if (!$emergencyManger->getEmergencyFeatureEnabled()) {
            throw $this->createNotFoundException();
        }

        $formHandler = $this->getEmergencyDraftFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_DRAFT_CREATED_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return FormInterface
     */
    private function getBusinessDraftForm() : FormInterface
    {
        $draft = $this->getEmergencyManager()->getEmergencyBusinessDraft();

        return $this->get('domain_emergency.form.business_draft')->setData($draft);
    }

    /**
     * @return EmergencyDraftBusinessFormHandler
     */
    private function getEmergencyDraftFormHandler() : EmergencyDraftBusinessFormHandler
    {
        return $this->get('domain_emergency.form.handler.draft');
    }

    /**
     * @return EmergencyManager
     */
    private function getEmergencyManager() : EmergencyManager
    {
        return $this->get('domain_emergency.manager.emergency');
    }
}
