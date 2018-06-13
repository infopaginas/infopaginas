<?php

namespace Domain\ReportBundle\Model\Exporter;

use Domain\ReportBundle\Model\ExporterInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PdfExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class PdfExporterModel implements ExporterInterface
{
    const FORMAT = 'pdf';

    /**
     * @var Pdf $pdfGenerator
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

    /**
     * @param string $html
     * @param string $filename
     * @param bool $print
     *
     * @return PdfResponse
     */
    protected function sendResponse($html, $filename, $print = false)
    {
        $content = $this->pdfGenerator->getOutputFromHtml($html);

        if ($print) {
            $dispositionType = ResponseHeaderBag::DISPOSITION_INLINE;
        } else {
            $dispositionType = ResponseHeaderBag::DISPOSITION_ATTACHMENT;
        }

        return new PdfResponse(
            $content,
            $filename,
            'application/pdf',
            $dispositionType
        );
    }
}
