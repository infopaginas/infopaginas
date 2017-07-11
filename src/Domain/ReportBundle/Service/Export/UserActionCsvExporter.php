<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\UserActionReportManager;
use Domain\ReportBundle\Model\Exporter\CsvPostponedExporterModel;
use Domain\ReportBundle\Model\UserActionModel;

/**
 * Class UserActionCsvExporter
 * @package Domain\ReportBundle\Export
 */
class UserActionCsvExporter extends CsvPostponedExporterModel
{
    /**
     * @var UserActionReportManager $userActionReportManager
     */
    protected $userActionReportManager;

    /**
     * @param UserActionReportManager $service
     */
    public function setUserActionReportManager(UserActionReportManager $service)
    {
        $this->userActionReportManager = $service;
    }

    /**
     * @param array  $parameters
     */
    protected function setData($parameters = [])
    {
        $dataIterator = $this->userActionReportManager->getUserActionReportExportDataIterator();
        $headers = UserActionReportManager::getUserActionReportMapping();

        $this->initProperties();

        foreach ($dataIterator as $item) {
            if ($this->isNewPage) {
                $path = $this->generateTempFilePath($parameters['exportPath'], $this->page);

                $this->createCsvResource($path);
                $this->generateHeaderTable($headers);

                $this->isNewPage = false;
            }

            $this->generateMainTable($item);
            $this->counter++;

            if ($this->counter >= self::MAX_ROW_PER_FILE) {
                $this->isNewPage = true;
                $this->counter   = 0;
                $this->page++;
            }
        }

        unset($dataIterator);
    }

    /**
     * @param array $rawData
     */
    protected function generateMainTable($rawData)
    {
        $data = $this->userActionReportManager->convertMongoDataToArray($rawData);

        $eventsMapping = UserActionModel::EVENT_TYPES;

        $row = [];

        foreach ($data as $key => $value) {
            if ($key == UserActionReportManager::MONGO_DB_FIELD_DATA) {
                $row[] = implode(',', $value);
            } elseif ($key == UserActionReportManager::MONGO_DB_FIELD_ACTION) {
                $row[] = $this->translator->trans($eventsMapping[$value], [], 'AdminReportBundle');
            } else {
                $row[] = $value;
            }
        }

        if ($row) {
            $this->writeToFile($row);
        }
    }

    /**
     * @param array $headers
     */
    protected function generateHeaderTable($headers)
    {
        $row = [];

        foreach ($headers as $name) {
            $row[] = $this->translator->trans($name, [], 'AdminReportBundle');
        }

        if ($row) {
            $this->writeToFile($row);
        }
    }
}
