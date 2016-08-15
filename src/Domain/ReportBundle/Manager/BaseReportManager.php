<?php

namespace Domain\ReportBundle\Manager;

use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;

class BaseReportManager extends DefaultManager
{
    /**
     * @param string $format
     * @param string $reportName
     * @return string
     */
    public function generateReportName(string $format, string $reportName) : string
    {
        $filename = sprintf(
            '%s_%s.%s',
            $reportName,
            date(self::REPORT_NAME_DATE_FORMAT, strtotime('now')),
            $format
        );

        return $filename;
    }
}