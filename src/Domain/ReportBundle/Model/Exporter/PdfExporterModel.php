<?php

namespace Domain\ReportBundle\Model\Exporter;

use Domain\ReportBundle\Model\ExporterInterface;
use Spraed\PDFGeneratorBundle\PDFGenerator\PDFGenerator;
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

    protected function sendResponse($html, $filename, $print = false)
    {
        $content = $this->pdfGenerator->generatePDF($html, 'UTF-8');

        if ($print) {
            $dispositionType = ResponseHeaderBag::DISPOSITION_INLINE;
        } else {
            $dispositionType = ResponseHeaderBag::DISPOSITION_ATTACHMENT;
        }

        return new Response(
            $content,
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('%s; filename=%s', $dispositionType, $filename)
            )
        );
    }
}
