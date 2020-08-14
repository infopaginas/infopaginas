<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Model\Exporter\CsvPostponedExporterModel;

/**
 * Class BusinessProfileCsvExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessProfileCsvExporter extends CsvPostponedExporterModel
{
    /**
     * @var BusinessProfileManager $businessProfileManager
     */
    protected $businessProfileManager;

    /**
     * @param BusinessProfileManager $service
     */
    public function setBusinessProfileManager(BusinessProfileManager $service)
    {
        $this->businessProfileManager = $service;
    }

    /**
     * @param array  $parameters
     */
    protected function setData($parameters = [])
    {
        $dataIterator = $this->businessProfileManager->getBusinessProfileExportDataIterator($parameters);

        $this->initProperties();

        foreach ($dataIterator as $item) {
            if ($this->isNewPage) {
                $path = $this->generateTempFilePath($parameters['exportPath'], $this->page);

                $this->createStreamResource($path);
                $this->generateHeaderTable(array_keys($item));

                $this->isNewPage = false;
            }

            $this->generateMainTable($item);
            $this->counter++;
            $this->em->clear();

            if ($this->counter >= self::MAX_ROW_PER_FILE) {
                $this->isNewPage = true;
                $this->counter   = 0;
                $this->page++;
            }
        }

        unset($dataIterator);
    }

    /**
     * @param array $data
     */
    protected function generateMainTable($data)
    {
        if ($data) {
            $this->writeToFile($data);
        }
    }

    /**
     * @param array $headers
     */
    protected function generateHeaderTable($headers)
    {
        $this->generateMainTable($headers);
    }
}
