<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Manager\BusinessApiManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Domain\ReportBundle\Manager\BusinessReportApiManager;

/**
 * Class ApiController
 */
class ApiController extends Controller
{
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
