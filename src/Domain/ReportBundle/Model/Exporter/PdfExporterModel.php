<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Model\Exporter;

use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\ReportBundle\Model\ExporterInterface;
use Spraed\PDFGeneratorBundle\PDFGenerator\PDFGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class PdfExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class PdfExporterModel implements ExporterInterface
{
    /**
     * @var PDFGenerator $pdfGenerator
     */
    protected $pdfGenerator;

    /**
     * @var EngineInterface $templateEngine
     */
    protected $templateEngine;

    /**
     * @param EngineInterface $service
     */
    public function setTemplateEngine(EngineInterface $service)
    {
        $this->templateEngine = $service;
    }

    public function setPdfGenerator($service)
    {
        $this->pdfGenerator = $service;
    }
}
