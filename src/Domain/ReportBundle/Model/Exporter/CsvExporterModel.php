<?php

namespace Domain\ReportBundle\Model\Exporter;

use Doctrine\ORM\EntityManager;
use Domain\ReportBundle\Model\ExporterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\Translator;

/**
 * Class CsvExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class CsvExporterModel implements ExporterInterface
{
    protected const FORMAT = 'csv';

    protected $files = [];

    /**
     * @var Translator $translator
     */
    protected $translator;

    /**
     * @var resource $csvResource
     */
    protected $csvResource;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @param Translator $service
     */
    public function setTranslator(Translator $service)
    {
        $this->translator = $service;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $path
     */
    protected function createCsvResource($path)
    {
        $this->csvResource = fopen($path, 'w+');

        $this->files[] = $path;
    }

    /**
     * @param array $row
     *
     * @return void
     */
    protected function writeToFile($row)
    {
        fputcsv($this->csvResource, $row);
    }

    protected function closeConnection()
    {
        fclose($this->csvResource);
    }

    /**
     * @param string $path
     * @param int    $page
     *
     * @return string
     */
    protected function generateTempFilePath($path, $page = 0)
    {
        return $path . uniqid('', true) . '_' . $page . '.' . self::FORMAT;
    }

    /**
     * @param string $filename
     *
     * @return Response
     */
    protected function sendDataResponse($filename)
    {
        rewind($this->csvResource);
        $response = new Response(stream_get_contents($this->csvResource));

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        // adding headers
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
