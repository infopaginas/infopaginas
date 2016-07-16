<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Model\Exporter;

use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\ReportBundle\Model\ExporterInterface;
use Domain\ReportBundle\Model\ReportInterface;
use Exporter\Source\SourceIteratorInterface;
use Liuggio\ExcelBundle\Factory;
use Sonata\CoreBundle\Exporter\Exporter as BaseExporter;
use Spraed\PDFGeneratorBundle\PDFGenerator\PDFGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class ExcelExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class ExcelExporterModel implements ExporterInterface
{
    /**
     * @var Factory $phpExcel
     */
    protected $phpExcel;

    /**
     * @var Translator $translator
     */
    protected $translator;

    /**
     * @param Factory $service
     */
    public function setPhpExcel(Factory $service)
    {
        $this->phpExcel = $service;
    }

    /**
     * @param Translator $service
     */
    public function setTranslator(Translator $service)
    {
        $this->translator = $service;
    }
}
