<?php

namespace Domain\ReportBundle\Model\Exporter;

/**
 * Class CsvPostponedExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class CsvPostponedExporterModel extends CsvExporterModel
{
    protected $counter   = 0;
    protected $page      = 1;
    protected $isNewPage = true;
    protected $files     = [];

    /**
     * @param array  $parameters
     */
    abstract protected function setData($parameters = []);

    /**
     * @param array $parameters
     * @return Array
     */
    public function getResponse($parameters = [])
    {
        $this->setData($parameters);

        return $this->files;
    }

    /**
     * @param string $path
     */
    protected function createCsvResource($path)
    {
        $this->csvResource = fopen($path, 'w');

        $this->files[] = $path;
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
     * @param array $row
     *
     * @return bool
     */
    protected function writeToFile($row)
    {
        fputcsv($this->csvResource, $row);
    }

    protected function closeConnection()
    {
        fclose($this->csvResource);
    }

    protected function initProperties()
    {
        $this->files     = [];
        $this->counter   = 0;
        $this->page      = 1;
        $this->isNewPage = true;
    }
}
