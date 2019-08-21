<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessApiManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Domain\ReportBundle\Manager\BusinessReportApiManager;

/**
 * Class ApiController
 */
class ApiController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_SOCIAL_NETWORK_CLICK_UPDATE = 'Successfully updated social network button click';

    const ERROR_SOCIAL_NETWORK_CLICK_UPDATE = 'An error occurred trying to update social network button click';

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function overviewAction(Request $request)
    {
        $params = $request->query->all();

        $result = $this->getBusinessReportApiManager()->getBusinessViewsAndImpressions($params);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function keywordsAction(Request $request)
    {
        $params = $request->query->all();

        $result = $this->getBusinessReportApiManager()->getBusinessKeywords($params);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchClosestBusinessesAction(Request $request)
    {
        $params = $this->getAllParamsFromRequest($request);
        $result = $this->getBusinessApiManager()->searchClosestBusinesses($params);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addBusinessPanoramaAction(Request $request)
    {
        $params = $this->getAllParamsFromRequest($request);
        $result = $this->getBusinessApiManager()->addBusinessPanorama($params);

        return new JsonResponse($result);
    }

    /**
     * @param $socialNetwork
     * @param $businessProfileId
     * @return JsonResponse
     */
    public function updateSocialButtonClickAction($socialNetwork, $businessProfileId)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getDoctrine()
            ->getRepository(BusinessProfile::class)
            ->find($businessProfileId);

        try {
            $this->getBusinessApiManager()->updateSocialButtonLink($socialNetwork, $businessProfile);
            return $this->getSuccessResponse(self::SUCCESS_SOCIAL_NETWORK_CLICK_UPDATE);
        } catch (\Exception $e) {
            return $this->getFailureResponse(self::ERROR_SOCIAL_NETWORK_CLICK_UPDATE);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getAllParamsFromRequest($request)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $params = $request->request->all();
        } else {
            $params = $request->query->all();
        }

        return $params;
    }

    /**
     * @return BusinessReportApiManager
     */
    private function getBusinessReportApiManager() : BusinessReportApiManager
    {
        return $this->get('domain_report.manager.business_report_api_manager');
    }

    /**
     * @return BusinessApiManager
     */
    private function getBusinessApiManager() : BusinessApiManager
    {
        return $this->get('domain_business.manager.business_api_manager');
    }
}
