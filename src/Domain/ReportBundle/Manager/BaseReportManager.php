<?php

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

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

    /**
     * @param string $periodOption
     *
     * @return array
     */
    public function handlePeriodOption($periodOption = '')
    {
        switch ($periodOption) {
            case AdminHelper::PERIOD_OPTION_CODE_PER_MONTH:
                $dateFormat = AdminHelper::DATE_MONTH_FORMAT;
                $step       = DatesUtil::STEP_MONTH;

                break;
            case AdminHelper::PERIOD_OPTION_CODE_WEEKLY:
                $dateFormat = AdminHelper::DATE_WEEK_FORMAT;
                $step       = DatesUtil::STEP_WEEK;

                break;
            default:
                $dateFormat = AdminHelper::DATE_FORMAT;
                $step       = DatesUtil::STEP_DAY;

                break;
        }

        return [
            $dateFormat,
            $step
        ];
    }
}
