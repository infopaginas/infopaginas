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
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getResponse($code, $format, AdminInterface $admin)
    {
        switch ($code) {
            case ReportInterface::CODE_PDF_SUBSCRIPTION_REPORT:
                $response = $this->container->get('domain_report.exporter.subscription_pdf_exporter')
                    ->getResponse($code, $format, $admin->getDatagrid()->getResults());
                break;
            case ReportInterface::CODE_EXCEL_SUBSCRIPTION_REPORT:
                $response = $this->container->get('domain_report.exporter.subscription_excel_exporter')
                    ->getResponse($code, $format, $admin->getDatagrid()->getResults());
                break;
            default;
                $filename = sprintf(
                    'export_%s_%s.%s',
                    strtolower(substr($admin->getClass(), strripos($admin->getClass(), '\\') + 1)),
                    date('Y_m_d_H_i_s', strtotime('now')),
                    $format
                );
                $exporter = new CoreExporter();
                $response = $exporter->getResponse($format, $filename, $admin->getDataSourceIterator());
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