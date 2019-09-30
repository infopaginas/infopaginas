<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\FeedbackReportManager;
use Domain\ReportBundle\Model\Exporter\CsvExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BusinessProfileCsvExporter
 * @package Domain\ReportBundle\Export
 */
class FeedbackReportCsvExporter extends CsvExporterModel
{
    const FILE_PATH = 'php://memory';

    /**
     * @var FeedbackReportManager $feedbackReportManager
     */
    protected $feedbackReportManager;

    /**
     * @param FeedbackReportManager $service
     */
    public function setFeedbackReportManager(FeedbackReportManager $service)
    {
        $this->feedbackReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = [])
    {
        $filename = $this->feedbackReportManager->generateReportName(self::FORMAT);

        $this->createCsvResource(self::FILE_PATH);
        $this->setData($params);

        return $this->sendDataResponse($filename);
    }

    /**
     * @param array $parameters
     */
    protected function setData($parameters = [])
    {
        $feedbackReportData = $this->feedbackReportManager->getFeedbackExportData($parameters);

        $isNewFile = true;
        foreach ($feedbackReportData['results'] as $item) {
            if ($isNewFile) {
                $this->generateMainTable(array_keys($item));
                $isNewFile = false;
            }

            $this->generateMainTable($item);
        }
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
}
