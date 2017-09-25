<?php

namespace Domain\BusinessBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\Impression;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessCloseRequestType;
use Domain\BusinessBundle\Form\Type\BusinessReportFilterType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\UserActionModel;
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
    /**
     * @param int $businessProfileId
     *
     * @return Response
     */
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function overviewAction(Request $request)
    {
        $params = $this->getExportParams($request);
        $data   = $this->prepareOverviewResponse($params);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function overviewAdminAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());
        $data   = $this->prepareOverviewResponse($params);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function adUsageAction(Request $request)
    {
        $params = $this->getExportParams($request);
        $data   = $this->prepareAdUsageResponse($params);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function adUsageAdminAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $params['businessProfile'] = $businessProfile;

        $data = $this->prepareAdUsageResponse($params);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function keywordsAction(Request $request)
    {
        $params = $this->getExportParams($request);
        $data   = $this->prepareKeywordsResponse($params);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function keywordsAdminAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());
        $data   = $this->prepareKeywordsResponse($params);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function eventTrackAction(Request $request)
    {
        $data   = $request->request->all();
        $result = false;

        foreach ($data as $key => $value) {
            switch ($key) {
                case BusinessOverviewModel::TYPE_CODE_IMPRESSION:
                case BusinessOverviewModel::TYPE_CODE_VIEW:
                    $result = $this->getBusinessOverviewReportManager()->registerBusinessEvent($key, $value);
                    break;
                case BusinessOverviewModel::TYPE_CODE_KEYWORD:
                    $result = $this->getKeywordsReportManager()->registerBusinessKeywordEvent($value);
                    break;
                case BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS:
                case BusinessOverviewModel::TYPE_CODE_CATEGORY_CATALOG:
                    $result = $this->getCategoryReportManager()->registerCategoryEvent($key, $value);
                    break;
            }
        }

        return new JsonResponse(
            [
                'status' => $result,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function excelExportAction(Request $request)
    {
        $params = $this->getExportParams($request);

        return $this->getExcelExporterService()->getResponse($params);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function excelAdminExportAction(Request $request)
    {
        $params = $this->getAdminExportParams($request);

        return $this->getExcelExporterService()->getResponse($params);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function pdfExportAction(Request $request)
    {
        $params = $this->getExportParams($request);

        return $this->getPdfExporterService()->getResponse($params);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function pdfAdminExportAction(Request $request)
    {
        $params = $this->getAdminExportParams($request);

        return $this->getPdfExporterService()->getResponse($params);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getExportParams(Request $request)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
        } else {
            $data = $request->query->all();
        }

        $params = $this->prepareReportParameters($data);

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $this->checkBusinessProfileAccess($businessProfile);

        $params['businessProfile'] = $businessProfile;

        return $params;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getAdminExportParams(Request $request)
    {
        $params = $this->prepareReportParameters($request->query->all());

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $params['businessProfile'] = $businessProfile;

        $this->userActionExportLog($businessProfile);

        return $params;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    protected function userActionExportLog($businessProfile)
    {
        $userActionReportManager = $this->get('domain_report.manager.user_action_report_manager');

        $id = $businessProfile->getId();

        $entityPath = explode('\\', get_class($businessProfile));
        $entityName = end($entityPath);

        $userActionReportManager->registerUserAction(
            UserActionModel::TYPE_ACTION_EXPORT,
            [
                'entity'        => $entityName,
                'entityName'    => (string) $businessProfile,
                'type'          => UserActionModel::TYPE_ACTION_EXPORT,
                'id'            => $id,
                'url' => $this->generateUrl(
                    'admin_domain_business_businessprofile_show',
                    [
                        'id' => $id,
                    ]
                ),
            ]
        );
    }

    /**
     * @param array $requestData
     *
     * @return array
     */
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

    /**
     * @param array $params
     *
     * @return array
     */
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

    /**
     * @param array $params
     *
     * @return array
     */
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

    /**
     * @param array $params
     *
     * @return array
     */
    protected function prepareAdUsageResponse($params)
    {
        $adUsageData = $this->getAdUsageReportManager()->getAdUsageData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/adUsageStatistics.html.twig',
            [
                'adUsageData' => $adUsageData,
            ]
        );

        return [
            'stats'       => $stats,
            'dates'       => $adUsageData['dates'],
            'clicks'      => $adUsageData['chart'][AdUsageReportManager::MONGO_DB_FIELD_CLICKS],
            'impressions' => $adUsageData['chart'][AdUsageReportManager::MONGO_DB_FIELD_IMPRESSIONS],
        ];
    }

    /**
     * @return \Domain\ReportBundle\Model\Exporter\PdfExporterModel
     */
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

    /**
     * @return KeywordsReportManager
     */
    protected function getKeywordsReportManager() : KeywordsReportManager
    {
        return $this->get('domain_report.manager.keywords_report_manager');
    }

    /**
     * @return CategoryReportManager
     */
    protected function getCategoryReportManager() : CategoryReportManager
    {
        return $this->get('domain_report.manager.category_report_manager');
    }

    /**
     * @return AdUsageReportManager
     */
    protected function getAdUsageReportManager() : AdUsageReportManager
    {
        return $this->get('domain_report.manager.ad_usage');
    }

    /**
     * @return BusinessOverviewReportManager
     */
    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->get('domain_report.manager.business_overview_report_manager');
    }

    /**
     * @return BusinessProfileManager
     */
    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @throws \Exception
     */
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
