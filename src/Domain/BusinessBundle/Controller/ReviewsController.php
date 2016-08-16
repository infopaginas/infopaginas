<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Form\Handler\ReviewFormHandler;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\BusinessReviewManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewsController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_REVIEW_CREATED_MESSAGE = 'Review has been successfully created. It\'ll be visible after approval.';
    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    const BUSINESS_NOT_FOUND_MESSAGE = 'Business profile is not found.';

    public function listAction(Request $request, int $businessProfileId)
    {
        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new NotFoundHttpException(self::BUSINESS_NOT_FOUND_MESSAGE);
        }

        $reviews = $this->getBusinessReviewsManager()->getReviewsForBusinessProfile($businessProfile);

        return $this->render('DomainBusinessBundle:Reviews:list.html.twig', [
            'businessProfile' => $businessProfile,
            'reviews'         => $reviews,
        ]);
    }

    public function saveAction(Request $request) : JsonResponse
    {
        $formHandler = $this->getReviewFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_REVIEW_CREATED_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @return ReviewFormHandler
     */
    private function getReviewFormHandler() : ReviewFormHandler
    {
        return $this->get('domain_business.form.handler.review');
    }

    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    private function getBusinessReviewsManager() : BusinessReviewManager
    {
        return $this->get('domain_business.manager.review');
    }
}
