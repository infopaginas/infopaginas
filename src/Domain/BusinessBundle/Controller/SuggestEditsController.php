<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Handler\BusinessProfileSuggestEditsFormHandler;
use Domain\BusinessBundle\Form\Type\BusinessSuggestEditsType;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SuggestEditsController
 * @package Domain\BusinessBundle\Controller
 */
class SuggestEditsController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_SUGGEST_EDITS_CREATED_MESSAGE = 'suggest_edits.response.success';
    const ERROR_VALIDATION_FAILURE              = 'suggest_edits.validation_message';

    /**
     * @param BusinessProfile $businessProfile
     * @param Request         $request
     *
     * @return RedirectResponse|Response
     */
    public function indexAction(BusinessProfile $businessProfile, Request $request)
    {
        if ($this->isBusinessProfileOwner($businessProfile)) {
            return $this->redirectToRoute('domain_business_profile_edit', ['id' => $businessProfile->getId()]);
        }

        $actionUrl = $this->generateUrl('domain_business_suggest_edits_save', ['slug' => $businessProfile->getSlug()]);

        $form = $this->createForm(BusinessSuggestEditsType::class, null, ['action' => $actionUrl]);

        return $this->render(':redesign:suggest-edits.html.twig', [
            'form'            => $form->createView(),
            'businessProfile' => $businessProfile,
            'locale'          => LocaleHelper::getLocale($request->getLocale()),
        ]);
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return JsonResponse
     */
    public function saveAction(BusinessProfile $businessProfile)
    {
        $formHandler = $this->getBusinessProfileSuggestEditsFormHandler();

        try {
            if (!$this->isBusinessProfileOwner($businessProfile) && $formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_SUGGEST_EDITS_CREATED_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return bool
     */
    private function isBusinessProfileOwner(BusinessProfile $businessProfile)
    {
        return $businessProfile->getUser()->getId() === $this->getUser()->getId();
    }

    /**
     * @return BusinessProfileSuggestEditsFormHandler
     */
    private function getBusinessProfileSuggestEditsFormHandler()
    {
        return $this->get('domain_business.form.handler.business_suggest_edits');
    }
}
