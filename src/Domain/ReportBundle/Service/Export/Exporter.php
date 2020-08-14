<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Entity\FeedbackReport;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\UserActionReport;
use Domain\ReportBundle\Entity\ViewAndImpressionReport;
use Domain\ReportBundle\Model\ReportInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Exporter
 * @package Domain\ReportBundle\Export
 */
class Exporter
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param string $entityClass
     * @param string $format
     * @param array $parameters
     * @param string $path
     *
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse|array
     */
    public function getResponse($entityClass, $format, $parameters = [], $path = '')
    {
        $parameters = $this->prepareFilterParameters($parameters, $path);
        $response   = [];

        switch ($entityClass) {
            case SubscriptionReport::class:
                switch ($format) {
                    case ReportInterface::FORMAT_PDF:
                        $response = $this->getSubscriptionPDFExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::FORMAT_EXCEL:
                        $response = $this->getSubscriptionExcelExporter()->getResponse($parameters);
                        break;
                }

                break;
            case CategoryReport::class:
                switch ($format) {
                    case ReportInterface::FORMAT_PDF:
                        $response = $this->getCategoryPDFExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::FORMAT_EXCEL:
                        $response = $this->getCategoryExcelExporter()->getResponse($parameters);
                        break;
                }

                break;
            case ViewAndImpressionReport::class:
                switch ($format) {
                    case ReportInterface::FORMAT_PDF:
                        $response = $this->getBusinessOverviewPDFExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::FORMAT_EXCEL:
                        $response = $this->getBusinessOverviewExcelExporter()->getResponse($parameters);
                        break;
                }

                break;
            case UserActionReport::class:
                switch ($format) {
                    case ReportInterface::FORMAT_EXCEL:
                        $response = $this->getUserActionExcelExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::FORMAT_CSV:
                        $response = $this->getUserActionCsvExporter()->getResponse($parameters);
                        break;
                }

                break;
            case BusinessProfile::class:
                switch ($format) {
                    case ReportInterface::FORMAT_EXCEL:
                        $response = $this->getBusinessProfileExcelExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::FORMAT_CSV:
                        $response = $this->getBusinessProfileCsvExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::PRINTING_LISTING:
                        $response = $this->getBusinessProfilePrintingListingExporter()->getResponse($parameters);
                        break;
                }

                break;
            case FeedbackReport::class:
                switch ($format) {
                    case ReportInterface::FORMAT_CSV:
                        $response = $this->getFeedbackReportCsvExporter()->getResponse($parameters);
                        break;
                }
                break;
        }

        return $response;
    }

    /**
     * @param array $params
     * @param string $path
     *
     * @return array
     */
    protected function prepareFilterParameters($params, $path)
    {
        $filterParameters = $params['filter'];

        unset($params['filter']);

        $params = array_merge($filterParameters, $params);

        $params['exportPath'] = $path;

        return $params;
    }

    /**
     * @param ContainerInterface $service
     */
    public function setContainer(ContainerInterface $service)
    {
        $this->container = $service;
    }

    /**
     * @return SubscriptionPdfExporter
     */
    protected function getSubscriptionPDFExporter()
    {
        return $this->container->get('domain_report.exporter.subscription_pdf_exporter');
    }

    /**
     * @return SubscriptionExcelExporter
     */
    protected function getSubscriptionExcelExporter()
    {
        return $this->container->get('domain_report.exporter.subscription_excel_exporter');
    }

    /**
     * @return CategoryPdfExporter
     */
    protected function getCategoryPDFExporter()
    {
        return $this->container->get('domain_report.exporter.category_pdf_exporter');
    }

    /**
     * @return CategoryExcelExporter
     */
    protected function getCategoryExcelExporter()
    {
        return $this->container->get('domain_report.exporter.category_excel_exporter');
    }

    /**
     * @return BusinessOverviewPdfExporter
     */
    protected function getBusinessOverviewPDFExporter()
    {
        return $this->container->get('domain_report.exporter.business_overview_pdf_exporter');
    }

    /**
     * @return BusinessOverviewExcelExporter
     */
    protected function getBusinessOverviewExcelExporter()
    {
        return $this->container->get('domain_report.exporter.business_overview_excel_exporter');
    }

    /**
     * @return UserActionExcelExporter
     */
    protected function getUserActionExcelExporter()
    {
        return $this->container->get('domain_report.exporter.user_action_excel_exporter');
    }

    /**
     * @return UserActionCsvExporter
     */
    protected function getUserActionCsvExporter()
    {
        return $this->container->get('domain_report.exporter.user_action_csv_exporter');
    }

    /**
     * @return BusinessProfileExcelExporter
     */
    protected function getBusinessProfileExcelExporter()
    {
        return $this->container->get('domain_report.exporter.business_profile_excel_exporter');
    }

    /**
     * @return BusinessProfileCsvExporter
     */
    protected function getBusinessProfileCsvExporter()
    {
        return $this->container->get('domain_report.exporter.business_profile_csv_exporter');
    }

    /**
     * @return FeedbackReportCsvExporter
     */
    protected function getFeedbackReportCsvExporter()
    {
        return $this->container->get('domain_report.exporter.feedback_report_csv_exporter');
    }

    protected function getBusinessProfilePrintingListingExporter()
    {
        return $this->container->get('domain_report.exporter.business_profile_printing_listing_exporter');
    }
}
