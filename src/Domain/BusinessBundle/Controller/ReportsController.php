<?php

namespace Domain\BusinessBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\Impression;
use Domain\BusinessBundle\Admin\BusinessProfileAdmin;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessChartFilterType;
use Domain\BusinessBundle\Form\Type\BusinessCloseRequestType;
use Domain\BusinessBundle\Form\Type\BusinessReportFilterType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Manager\GeolocationManager;
use Domain\ReportBundle\Manager\SocialNetworksReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\ExporterInterface;
use Domain\ReportBundle\Model\ReportInterface;
use Domain\ReportBundle\Model\UserActionModel;
use Domain\ReportBundle\Service\Export\BusinessAdsReportExcelExporter;
use Domain\ReportBundle\Service\Export\BusinessInteractionReportExcelExporter;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_LAST_MONTH);

        $params = [
            'businessProfileId' => $businessProfileId,
            'date'              => DatesUtil::getDateAsArrayFromVO($dateRange),
            'chartType'         => BusinessOverviewModel::DEFAULT_CHART_TYPE,
            'limit'             => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
            'periodOption'      => AdminHelper::PERIOD_OPTION_CODE_PER_MONTH,
        ];

        $businessOverviewReportManager = $this->getBusinessOverviewReportManager();
        $overviewData = $businessOverviewReportManager->getBusinessOverviewReportData($params);
        $keywordsData = $this->prepareKeywordsResponse($params);

        $filtersForm = $this->createForm(new BusinessReportFilterType());

        $closeBusinessProfileForm = $this->createForm(new BusinessCloseRequestType());

        return $this->render(
            ':redesign:business-profile-report.html.twig',
            [
                'overviewData'             => $overviewData,
                'keywordData'              => $keywordsData,
                'eventList'                => BusinessOverviewModel::EVENT_TYPES,
                'filtersForm'              => $filtersForm->createView(),
                'businessProfileId'        => $businessProfileId,
                'businessProfile'          => $businessProfile,
                'closeBusinessProfileForm' => $closeBusinessProfileForm->createView(),
                'exportPdf'                => ReportInterface::FORMAT_PDF,
                'exportExcel'              => ReportInterface::FORMAT_EXCEL,
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
    public function socialNetworksAdminAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());
        $data   = $this->prepareSocialNetworksResponse($params);

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
                case BusinessOverviewModel::TYPE_CODE_GEOLOCATION:
                    $result = $this->getGeolocationManager()->registerGeolocationEvent($value);
                    break;
                case BusinessOverviewModel::TYPE_CODE_KEYWORD:
                    $result = $this->getKeywordsReportManager()->registerBusinessKeywordEvent($value);
                    break;
                case BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS:
                case BusinessOverviewModel::TYPE_CODE_CATEGORY_CATALOG:
                    $result = $this->getCategoryOverviewReportManager()->registerCategoryEvent(
                        $key,
                        $value
                    );
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
     * @param string  $format
     *
     * @return Response
     */
    public function adsExportAction(Request $request, $format)
    {
        $params   = $this->getExportParams($request, true);
        $exporter = $this->getAdsExporterByFormat($format);

        return $exporter->getResponse($params);
    }

    /**
     * @param Request $request
     * @param string  $format
     *
     * @return Response
     */
    public function interactionExportAction(Request $request, $format)
    {
        $params   = $this->getExportParams($request, true);
        $exporter = $this->getInteractionExporterByFormat($format);

        return $exporter->getResponse($params);
    }

    /**
     * @param Request $request
     * @param string  $format
     *
     * @return Response
     */
    public function adsAdminExportAction(Request $request, $format)
    {
        $params   = $this->getAdminExportParams($request);
        $exporter = $this->getAdsExporterByFormat($format);

        return $exporter->getResponse($params);
    }

    /**
     * @param Request $request
     * @param string  $format
     *
     * @return Response
     */
    public function interactionAdminExportAction(Request $request, $format)
    {
        $params   = $this->getAdminExportParams($request);
        $exporter = $this->getInteractionExporterByFormat($format);

        return $exporter->getResponse($params);
    }

    /**
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     */
    public function chartPreviewAction($id)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfileManager()->find($id);
        $this->checkBusinessProfileAccess($businessProfile);

        if (!$businessProfile) {
            throw $this->createNotFoundException();
        }

        $filtersForm = $this->createForm(new BusinessChartFilterType());

        return $this->render(':redesign:chart-preview.html.twig', [
            'business' => $businessProfile,
            'filtersForm' => $filtersForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function chartExportAction(Request $request)
    {
        $params   = $this->getChartExportParams($request);
        $exporter = $this->get('domain_report.charts_exporter.pdf');

        return $exporter->getResponse($params);
    }

    /**
     * @param Request $request
     * @param bool    $isExport
     *
     * @return array
     */
    protected function getExportParams(Request $request, $isExport = false)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
        } else {
            $data = $request->query->all();
        }

        $params = $this->prepareReportParameters($data, $isExport);

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
        $params = $this->prepareReportParameters($request->query->all(), true);

        $businessProfile = $this->getBusinessProfileManager()->find($params['businessProfileId']);

        $params['businessProfile'] = $businessProfile;

        $this->userActionExportLog($businessProfile);

        return $params;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    protected function getChartExportParams(Request $request)
    {
        $params['charts'] = $request->request->get('chart', []);
        $params['dates'] = $request->request->get('date', []);
        $params['statisticsTableData'] = $request->request->get('statisticsTableData', []);
        $params['print'] = $request->request->get('print', false);
        $businessProfile = $this->getBusinessProfileManager()->find($request->request->get('businessId'));

        $this->checkBusinessProfileOrAdminAccess($businessProfile);

        $params['businessProfile'] = $businessProfile;

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
     * @param bool  $isExport
     *
     * @return array
     */
    protected function prepareReportParameters($requestData, $isExport = false)
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

        if (!empty($requestData['periodOption']) and
            !empty(AdminHelper::getPeriodOptionValues()[$requestData['periodOption']])
        ) {
            $params['periodOption'] = $requestData['periodOption'];
        }

        if (!$isExport) {
            if (!empty($requestData['chartType']) and
                in_array($requestData['chartType'], BusinessOverviewModel::getChartEventTypes())
            ) {
                $params['chartType'] = $requestData['chartType'];
            }
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

        $translator = $this->get('translator');

        return [
            'stats'       => $stats,
            'dates'       => $overviewData['dates'],
            'chart'       => $overviewData['chart'],
            'chartTitle'  => $translator->trans($overviewData['chartTitle']),
            'chartHint'   => $translator->trans($overviewData['chartHint']),
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
    protected function prepareSocialNetworksResponse($params)
    {
        $socialNetworksReportManager = $this->getSocialNetworksReportManager();
        $socialNetworksData = $socialNetworksReportManager->getSocialNetworkData($params);

        return [
            'socialNetworks' => $socialNetworksData['socialNetworks'],
            'clicks' => $socialNetworksData['clicks'],
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
    protected function getAdsPdfExporterService()
    {
        return $this->get('domain_report.ads_exporter.pdf');
    }

    /**
     * @return \Domain\ReportBundle\Model\Exporter\PdfExporterModel
     */
    protected function getInteractionPdfExporterService()
    {
        return $this->get('domain_report.interaction_exporter.pdf');
    }

    /**
     * @return \Domain\ReportBundle\Service\Export\BusinessInteractionReportExcelExporter
     */
    protected function getInteractionExcelExporterService() : BusinessInteractionReportExcelExporter
    {
        return $this->get('domain_report.interaction_exporter.excel');
    }

    /**
     * @return \Domain\ReportBundle\Service\Export\BusinessAdsReportExcelExporter
     */
    protected function getAdsExcelExporterService() : BusinessAdsReportExcelExporter
    {
        return $this->get('domain_report.ads_exporter.excel');
    }

    /**
     * @return KeywordsReportManager
     */
    protected function getKeywordsReportManager() : KeywordsReportManager
    {
        return $this->get('domain_report.manager.keywords_report_manager');
    }

    /**
     * @return SocialNetworksReportManager
     */
    protected function getSocialNetworksReportManager() : SocialNetworksReportManager
    {
        return $this->get('domain_report.manager.social_networks_report_manager');
    }

    /**
     * @return CategoryOverviewReportManager
     */
    protected function getCategoryOverviewReportManager() : CategoryOverviewReportManager
    {
        return $this->get('domain_report.manager.category_overview_report_manager');
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
     * @return GeolocationManager
     */
    protected function getGeolocationManager() : GeolocationManager
    {
        return $this->get('domain_report.manager.geolocation_manager');
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

    /**
     * @param BusinessProfile $businessProfile
     *
     * @throws \Exception
     */
    protected function checkBusinessProfileOrAdminAccess(BusinessProfile $businessProfile)
    {
        $user    = $this->getUser();
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SALES_MANAGER');

        if (!$user || !($user instanceof User)) {
            throw $this->createNotFoundException();
        }

        if (!(($businessProfile->getUser() and $businessProfile->getUser()->getId() == $user->getId()) || $isAdmin)) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * @param string $format
     *
     * @return ExporterInterface
     */
    protected function getInteractionExporterByFormat($format)
    {
        if ($format == ReportInterface::FORMAT_PDF) {
            $exporter = $this->getInteractionPdfExporterService();
        } else {
            $exporter = $this->getInteractionExcelExporterService();
        }

        return $exporter;
    }

    /**
     * @param string $format
     *
     * @return ExporterInterface
     */
    protected function getAdsExporterByFormat($format)
    {
        if ($format == ReportInterface::FORMAT_PDF) {
            $exporter = $this->getAdsPdfExporterService();
        } else {
            $exporter = $this->getAdsExcelExporterService();
        }

        return $exporter;
    }
}
