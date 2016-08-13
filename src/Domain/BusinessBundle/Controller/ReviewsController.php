<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Form\Handler\ReviewFormHandler;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewsController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_REVIEW_CREATED_MESSAGE = 'Review has been successfully created. It\'ll be visible after approval.';
    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

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

    public function editAction(Request $request, int $id)
    {

    }

    public function viewAction(Request $request, int $id)
    {

    }

    public function deleteAction(Request $request, int $id)
    {

    }

    /**
     * @return ReviewFormHandler
     */
    private function getReviewFormHandler() : ReviewFormHandler
    {
        return $this->get('domain_business.form.handler.review');
    }
}
