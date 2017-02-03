<?php

namespace Domain\BusinessBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Domain\ReportBundle\Manager\BusinessOverviewReportApiManager;

/**
 * Class ApiController
 */
class ApiController extends Controller
{
    public function overviewAction(Request $request)
    {
        $params = $request->query->all();

        $result = $this->getBusinessOverviewReportApiManager()->getBusinessOverview($params);

        return new JsonResponse($result);
    }

    private function getBusinessOverviewReportApiManager() : BusinessOverviewReportApiManager
    {
        return $this->get('domain_report.manager.business_overview_report_api_manager');
    }
}
