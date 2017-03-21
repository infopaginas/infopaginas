<?php

namespace Domain\BusinessBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\Impression;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessCloseRequestType;
use Domain\BusinessBundle\Form\Type\BusinessReportFilterType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Service\Export\BusinessReportExcelExporter;
use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Oxa\Sonata\UserBundle\Entity\User;

/**
 * Class ReportController
 * @package Domain\BusinessBundle\Controller
 */
class ReportsController extends Controller
{
    public function indexAction(int $businessProfileId)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        $this->checkBusinessProfileAccess($businessProfile);

        $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_DEFAULT);

        $params = [
            'businessProfileId' => $businessProfileId,
            'date'              => DatesUtil::getDateAsArrayFromVO($dateRange),
        ];

        $businessOverviewReportManager = $this->getBusinessOverviewReportManager();
        $overviewData = $businessOverviewReportManager->getBusinessOverviewReportData($params);

        $filtersForm = $this->createForm(new BusinessReportFilterType());

        $closeBusinessProfileForm = $this->createForm(new BusinessCloseRequestType());

        return $this->render(
            ':redesign:business-profile-report.html.twig',
            [
                'overviewData'             => $overviewData,
                'eventList'                => BusinessOverviewModel::EVENT_TYPES,
                'filtersForm'              => $filtersForm->createView(),
                'businessProfileId'        => $businessProfileId,
                'businessProfile'          => $businessProfile,
                'closeBusinessProfileForm' => $closeBusinessProfileForm->createView(),
            ]
        );
    }

    public function overviewAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $this->checkBusinessProfileAccess($businessProfile);

        $data = $this->prepareOverviewResponse($params);

        return new JsonResponse($data);
    }

    public function overviewAdminAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());
        $data   = $this->prepareOverviewResponse($params);

        return new JsonResponse($data);
    }

    public function adUsageAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());

        $businessAdUsageReportManager = $this->getAdUsageReportManager();
        $adUsageData = $businessAdUsageReportManager->getAdUsageData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/adUsageStatistics.html.twig',
            [
                'adUsageData' => $adUsageData,
            ]
        );

        return new JsonResponse([
            'stats' => $stats,
        ]);
    }

    public function keywordsAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $this->checkBusinessProfileAccess($businessProfile);

        $data = $this->prepareKeywordsResponse($params);

        return new JsonResponse($data);
    }

    public function keywordsAdminAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());
        $data   = $this->prepareKeywordsResponse($params);

        return new JsonResponse($data);
    }

    public function interactionsTrackAction(Request $request)
    {
        $businessProfileId = $request->request->get('id', null);
        $type = $request->request->get('type', null);

        $result = $this->getBusinessOverviewReportManager()->registerBusinessInteraction($businessProfileId, $type);

        return new JsonResponse(
            [
                'status' => $result,
            ]
        );
    }

    public function excelExportAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->query->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $this->checkBusinessProfileAccess($businessProfile);

        $params['businessProfile'] = $businessProfile;

        return $this->getExcelExporterService()->getResponse($params);
    }

    public function excelAdminExportAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->query->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $params['businessProfile'] = $businessProfile;

        return $this->getExcelExporterService()->getResponse($params);
    }

    public function pdfExportAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->query->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $this->checkBusinessProfileAccess($businessProfile);

        $params['businessProfile'] = $businessProfile;

        return $this->getPdfExporterService()->getResponse($params);
    }

    public function pdfAdminExportAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->query->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $params['businessProfile'] = $businessProfile;

        return $this->getPdfExporterService()->getResponse($params);
    }

    protected function prepareReportParameters($requestData)
    {
        $params = [
            'businessProfileId' => $requestData['businessProfileId'],
            'limit'             => $requestData['limit'],
        ];

        if ($requestData['datesRange'] !== DatesUtil::RANGE_CUSTOM) {
            $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType($requestData['datesRange']);
            $params['date'] = DatesUtil::getDateAsArrayFromVO($dateRange);
        } else {
            $params['date'] = DatesUtil::getDateAsArrayFromRequestData($requestData);
        }

        if (!empty($requestData['print'])) {
            $params['print'] = true;
        } else {
            $params['print'] = false;
        }

        return $params;
    }

    protected function prepareOverviewResponse($params)
    {
        $businessOverviewReportManager = $this->getBusinessOverviewReportManager();
        $overviewData = $businessOverviewReportManager->getBusinessOverviewReportData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/businessOverviewStatistics.html.twig',
            [
                'overviewData' => $overviewData,
                'eventList'    => BusinessOverviewModel::EVENT_TYPES,
            ]
        );

        return [
            'stats'       => $stats,
            'dates'       => $overviewData['dates'],
            'views'       => $overviewData['views'],
            'impressions' => $overviewData['impressions'],
        ];
    }

    protected function prepareKeywordsResponse($params)
    {
        $keywordsReportManager = $this->getKeywordsReportManager();
        $keywordsData = $keywordsReportManager->getKeywordsData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/keywordStatistics.html.twig',
            [
                'keywordsData' => $keywordsData,
            ]
        );

        return [
            'stats'    => $stats,
            'keywords' => $keywordsData['keywords'],
            'searches' => $keywordsData['searches'],
        ];
    }

    protected function getPdfExporterService()
    {
        return $this->get('domain_report.exporter.pdf');
    }

    /**
     * @return \Domain\ReportBundle\Service\Export\BusinessReportExcelExporter
     */
    protected function getExcelExporterService() : BusinessReportExcelExporter
    {
        return $this->get('domain_report.exporter.excel');
    }

    protected function getKeywordsReportManager() : KeywordsReportManager
    {
        return $this->get('domain_report.manager.keywords_report_manager');
    }

    protected function getAdUsageReportManager() : AdUsageReportManager
    {
        return $this->get('domain_report.manager.ad_usage');
    }

    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->get('domain_report.manager.business_overview_report_manager');
    }

    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    protected function checkBusinessProfileAccess(BusinessProfile $businessProfile)
    {
        $token = $this->get('security.context')->getToken();
        if (!$token) {
            throw $this->createNotFoundException();
        }

        $user = $token->getUser();

        if (!$user || !($user instanceof User)) {
            throw $this->createNotFoundException();
        }

        if (!($businessProfile->getUser() and $businessProfile->getUser()->getId() == $user->getId())) {
            throw $this->createNotFoundException();
        }
    }
}
