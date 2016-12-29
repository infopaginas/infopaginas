<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Model\ReportInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\CoreBundle\Exporter\Exporter as CoreExporter;

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
     * @param $code
     * @param $format
     * @param AdminInterface $admin
     * @param array $parameters
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getResponse($code, $format, AdminInterface $admin, $parameters = [])
    {
        $params = array_merge($admin->getFilterParameters(), $parameters);

        switch ($code) {
            case ReportInterface::CODE_PDF_SUBSCRIPTION_REPORT:
                $response = $this->container->get('domain_report.exporter.subscription_pdf_exporter')
                    ->getResponse($code, $format, $admin->getDatagrid()->getResults(), $parameters);
                break;
            case ReportInterface::CODE_EXCEL_SUBSCRIPTION_REPORT:
                $response = $this->container->get('domain_report.exporter.subscription_excel_exporter')
                    ->getResponse($code, $format, $admin->getDatagrid()->getResults(), $parameters);
                break;
            case ReportInterface::CODE_PDF_CATEGORY_REPORT:
                $response = $this->container->get('domain_report.exporter.category_pdf_exporter')
                    ->getResponse($code, $format, $params);
                break;
            case ReportInterface::CODE_EXCEL_CATEGORY_REPORT:
                $response = $this->container->get('domain_report.exporter.category_excel_exporter')
                    ->getResponse($code, $format, $params);
                break;
            case ReportInterface::CODE_PDF_BUSINESS_OVERVIEW_REPORT:
                $response = $this->container->get('domain_report.exporter.business_overview_pdf_exporter')
                    ->getResponse($code, $format, $params);
                break;
            case ReportInterface::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT:
                $response = $this->container->get('domain_report.exporter.business_overview_excel_exporter')
                    ->getResponse($code, $format, $params);
                break;
            default:
                $filename = $this->container->get('domain_report.manager.category_report_manager')->generateReportName(
                    $format,
                    strtolower(substr($admin->getClass(), strripos($admin->getClass(), '\\') + 1))
                );

                $exporter = new CoreExporter();
                $response = $exporter->getResponse($format, $filename, $admin->getDataSourceIterator());
                break;
        }

        return $response;
    }

    /**
     * @param ContainerInterface $service
     */
    public function setContainer(ContainerInterface $service)
    {
        $this->container = $service;
    }
}
