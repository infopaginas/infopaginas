<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\Visitor;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\ReportInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

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
     *
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getResponse($entityClass, $format, $parameters = [])
    {
        $parameters = $this->prepareFilterParameters($parameters);
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
            case Visitor::class:
                switch ($format) {
                    case ReportInterface::FORMAT_PDF:
                        $response = $this->getBusinessOverviewPDFExporter()->getResponse($parameters);
                        break;
                    case ReportInterface::FORMAT_EXCEL:
                        $response = $this->getBusinessOverviewExcelExporter()->getResponse($parameters);
                        break;
                }

                break;
        }

        return $response;
    }

    protected function prepareFilterParameters($params)
    {
        $filterParameters = $params['filter'];

        unset($params['filter']);

        $params = array_merge($filterParameters, $params);

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
     * @return CategoryReportManager
     */
    protected function getCategoryReportManager()
    {
        return $this->container->get('domain_report.manager.category_report_manager');
    }
}
