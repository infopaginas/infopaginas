<?php

namespace Domain\BusinessBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Domain\ReportBundle\Manager\BusinessReportApiManager;

/**
 * Class ApiController
 */
class ApiController extends Controller
{
    public function overviewAction(Request $request)
    {
        $params = $request->query->all();

        $result = $this->getBusinessReportApiManager()->getBusinessViewsAndImpressions($params);

        return new JsonResponse($result);
    }

    public function keywordsAction(Request $request)
    {
        $params = $request->query->all();

        $result = $this->getBusinessReportApiManager()->getBusinessKeywords($params);

        return new JsonResponse($result);
    }

    private function getBusinessReportApiManager() : BusinessReportApiManager
    {
        return $this->get('domain_report.manager.business_report_api_manager');
    }
}
