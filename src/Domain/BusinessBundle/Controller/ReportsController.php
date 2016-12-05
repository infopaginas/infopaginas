<?php

namespace Domain\BusinessBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\Impression;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Type\BusinessReportFilterType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Entity\Keyword;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\InteractionsReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Service\Export\BusinessReportExcelExporter;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ReportController
 * @package Domain\BusinessBundle\Controller
 */
class ReportsController extends Controller
{
    public function indexAction(int $businessProfileId)
    {
        $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType(DatesUtil::RANGE_DEFAULT);

        $params = [
            'businessProfileId' => $businessProfileId,
            'date' => DatesUtil::getDateAsArrayFromVO($dateRange),
            'limit' => KeywordsReportManager::DEFAULT_KEYWORDS_COUNT,
        ];

        $businessOverviewReportManager = $this->getBusinessOverviewReportManager();
        $overviewData = $businessOverviewReportManager->getBusinessOverviewData($params);

        $filtersForm = $this->createForm(new BusinessReportFilterType());

        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        return $this->render(':redesign:business-profile-report.html.twig', [
            'overviewData'      => $overviewData,
            'filtersForm'       => $filtersForm->createView(),
            'businessProfileId' => $businessProfileId,
            'businessProfile'   => $businessProfile
        ]);
    }

    public function overviewAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());

        $businessOverviewReportManager = $this->getBusinessOverviewReportManager();
        $overviewData = $businessOverviewReportManager->getBusinessOverviewData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/businessOverviewStatistics.html.twig',
            [
                'overviewData' => $overviewData,
            ]
        );

        return new JsonResponse([
            'stats'       => $stats,
            'dates'       => $overviewData['dates'],
            'views'       => $overviewData['views'],
            'impressions' => $overviewData['impressions'],
        ]);
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

        $keywordsReportManager = $this->getKeywordsReportManager();
        $keywordsData = $keywordsReportManager->getKeywordsData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/keywordStatistics.html.twig',
            [
                'keywordsData' => $keywordsData,
            ]
        );

        return new JsonResponse([
            'stats'    => $stats,
            'keywords' => $keywordsData['keywords'],
            'searches' => $keywordsData['searches'],
        ]);
    }

    public function interactionsAction(Request $request)
    {
        $params = $this->prepareReportParameters($request->request->all());

        $interactionsReportManager = $this->getInteractionsReportManager();
        $interactionsData = $interactionsReportManager->getInteractionsData($params);

        $stats = $this->renderView(
            'DomainBusinessBundle:Reports:blocks/interactionStatistics.html.twig',
            [
                'interactionsData' => $interactionsData,
            ]
        );

        return new JsonResponse([
            'stats' => $stats,
        ]);
    }

    public function excelExportAction(Request $request)
    {
        $params = $request->query->all();
        return $this->getExcelExporterService()->export($this->prepareReportParameters($params));
    }

    public function pdfExportAction(Request $request)
    {
        $params = $request->query->all();
        list($filename, $content) = $this->getPdfExporterService()->export($this->prepareReportParameters($params));

        return new Response(
            $content,
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename=%s', $filename)
            )
        );
    }

    public function printAction(Request $request)
    {
        $params = $request->request->all();
        list($filename, $content) = $this->getPdfExporterService()->export($this->prepareReportParameters($params));

        $pdfPath = $this->getParameter('assetic.write_to') . '/uploads/' . $filename;

        file_put_contents($pdfPath, $content);

        $url = $this->get('request')->getUriForPath('/uploads/' . $filename);

        return new JsonResponse([
            'pdf' => str_replace('/app_dev.php', '', $url)
        ]);
    }

    protected function prepareReportParameters($requestData)
    {
        $params = [
            'businessProfileId' => $requestData['businessProfileId'],
            'limit' => $requestData['limit'],
        ];

        if ($requestData['datesRange'] !== 'custom') {
            $dateRange = DatesUtil::getDateRangeValueObjectFromRangeType($requestData['datesRange']);
            $params['date'] = DatesUtil::getDateAsArrayFromVO($dateRange);
        } else {
            $params['date'] = DatesUtil::getDateAsArrayFromRequestData($requestData);
        }

        return $params;
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

    protected function getInteractionsReportManager() : InteractionsReportManager
    {
        return $this->get('domain_report.manager.interactions');
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
}
