<?php

namespace Domain\ReportBundle\Manager;

use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;

class BaseReportManager extends DefaultManager
{
    protected $reportName = 'default_report';

    /**
     * @param string $format
     *
     * @return string
     */
    public function generateReportName(string $format) : string
    {
        $filename = sprintf(
            '%s_%s.%s',
            $this->reportName,
            date(self::REPORT_NAME_DATE_FORMAT, strtotime('now')),
            $format
        );

        return $filename;
    }
}
