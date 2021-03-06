<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BannerBundle\Model\TypeInterface;
use Domain\BusinessBundle\Form\Handler\ReviewFormHandler;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\BusinessReviewManager;
use Domain\BusinessBundle\Model\DataType\ReviewsListQueryParamsDTO;
use Domain\BusinessBundle\Model\DataType\ReviewsResultsDTO;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Domain\SearchBundle\Util\SearchDataUtil;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ReviewsController
 * @package Domain\BusinessBundle\Controller
 */
class ReviewsController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_REVIEW_CREATED_MESSAGE = 'Review has been successfully created. It\'ll be visible after approval.';
    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    const BUSINESS_NOT_FOUND_MESSAGE = 'Business profile is not found.';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        // see https://jira.oxagile.com/browse/INFT-2093
        throw $this->createNotFoundException();

        $businessProfileId = (int)$request->get('businessProfileId');
        $locale = LocaleHelper::getLocale($request->getLocale());

        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId, $locale);

        if (!$businessProfile) {
            throw new NotFoundHttpException(self::BUSINESS_NOT_FOUND_MESSAGE);
        }

        $paramsDTO = $this->getReviewsListQueryParamsDTO($request);

        $reviewsResultDTO = $this->getBusinessReviewsManager()
            ->getBusinessProfileReviewsResultDTO($businessProfile, $paramsDTO);

        $schema = $this->getBusinessProfileManager()->buildBusinessProfileReviewsSchema(
            $reviewsResultDTO->resultSet,
            $businessProfile
        );

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_BUSINESS_PAGE_RIGHT,
                TypeInterface::CODE_BUSINESS_PAGE_BOTTOM,
            ]
        );

        return $this->render(':redesign:review-list.html.twig', [
            'businessProfile'  => $businessProfile,
            'reviewsResultDTO' => $reviewsResultDTO,
            'schemaJsonLD'     => $schema,
            'banners'          => $banners,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function reviewListAdminAction(Request $request)
    {
        $businessProfileId = (int)$request->get('businessProfileId');

        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new NotFoundHttpException(self::BUSINESS_NOT_FOUND_MESSAGE);
        }

        $paramsDTO = $this->getReviewsListQueryParamsDTO($request);

        $reviewsResultDTO = $this->getBusinessReviewsManager()
            ->getBusinessProfileReviewsResultDTO($businessProfile, $paramsDTO);

        $data = $this->prepareBusinessReviewList($reviewsResultDTO);

        return new JsonResponse($data);
    }

    /**
     * @param ReviewsResultsDTO $reviewsResultsDTO
     *
     * @return array
     */
    protected function prepareBusinessReviewList($reviewsResultsDTO)
    {
        $reviewList = $this->renderView(
            ':redesign/blocks/review:review-list-ajax.html.twig',
            [
                'reviews' => $reviewsResultsDTO->resultSet,
            ]
        );

        return [
            'data'        => $reviewList,
            'page'        => $reviewsResultsDTO->page,
            'pageCount'   => $reviewsResultsDTO->pageCount,
            'resultCount' => $reviewsResultsDTO->resultCount,
        ];
    }

    /**
     * @return ReviewFormHandler
     */
    private function getReviewFormHandler() : ReviewFormHandler
    {
        return $this->get('domain_business.form.handler.review');
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @return BusinessReviewManager
     */
    private function getBusinessReviewsManager() : BusinessReviewManager
    {
        return $this->get('domain_business.manager.review');
    }

    /**
     * @param Request $request
     * @return ReviewsListQueryParamsDTO
     */
    private function getReviewsListQueryParamsDTO(Request $request)
    {
        $limit = (int)$this->get('oxa_config')->getSetting(ConfigInterface::DEFAULT_RESULTS_PAGE_SIZE)->getValue();
        $page = SearchDataUtil::getPageFromRequest($request);

        return new ReviewsListQueryParamsDTO($limit, $page);
    }
}
