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

    /**
     * @param array  $parameters
     */
    abstract protected function setData($parameters = []);

    /**
     * @param array $parameters
     * @return array
     */
    public function getResponse($parameters = [])
    {
        $this->setData($parameters);

        return $this->files;
    }

    protected function initProperties()
    {
        $this->files     = [];
        $this->counter   = 0;
        $this->page      = 1;
        $this->isNewPage = true;
    }
}
