<?php

namespace Domain\ReportBundle\Model\Exporter;

use Domain\ReportBundle\Model\ExporterInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class CsvExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class CsvExporterModel implements ExporterInterface
{
    const FORMAT = 'csv';

    /**
     * @var Translator $translator
     */
    protected $translator;

    /**
     * @var resource $csvResource
     */
    protected $csvResource;

    /**
     * @param Translator $service
     */
    public function setTranslator(Translator $service)
    {
        $this->translator = $service;
    }
}
