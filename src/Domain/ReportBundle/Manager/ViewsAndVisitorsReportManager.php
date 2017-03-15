<?php

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class ViewsAndVisitorsReportManager
 * @package Domain\ReportBundle\Manager
 */
class ViewsAndVisitorsReportManager
{
    const TYPE_SUM_INTERACTIONS = 'interactions';

    /** @var BusinessOverviewReportManager $businessOverviewReportManager */
    protected $businessOverviewReportManager;

    public function __construct(BusinessOverviewReportManager $businessOverviewReportManager)
    {
        $this->businessOverviewReportManager = $businessOverviewReportManager;
    }

    public function getViewsAndVisitorsData(array $params = [])
    {
        $params['businessProfileId'] = 0;

        $result = [
            'dates' => [],
            BusinessOverviewModel::TYPE_CODE_IMPRESSION => [],
            BusinessOverviewModel::TYPE_CODE_VIEW       => [],
            'results' => [],
            'datePeriod' => [
                'start' => $params['date']['value']['start'],
                'end'   => $params['date']['value']['end'],
            ],
            'total' => []
        ];

        $dates = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['value']['start'],
            $params['date']['value']['end']
        );

        if (!empty($params['periodOption']['value']) &&
            $params['periodOption']['value'] == AdminHelper::PERIOD_OPTION_CODE_PER_MONTH
        ) {
            $dateFormat = AdminHelper::DATE_MONTH_FORMAT;
            $step       = DatesUtil::STEP_MONTH;
        } else {
            $dateFormat = AdminHelper::DATE_FORMAT;
            $step       = DatesUtil::STEP_DAY;
        }

        $result['dates'] = DatesUtil::dateRange($dates, $step, $dateFormat);

        $params['dateObject'] = $dates;

        $overviewResult = $this->businessOverviewReportManager->getBusinessInteractionData($params);

        $businessProfileResult = $this->prepareBusinessOverviewReportStats(
            $result['dates'],
            $overviewResult,
            $dateFormat
        );

        $result['results'] = $businessProfileResult['results'];
        $result['total']   = $businessProfileResult['total'];

        $viewKey       = BusinessOverviewModel::TYPE_CODE_VIEW;
        $impressionKey = BusinessOverviewModel::TYPE_CODE_IMPRESSION;

        $result[$viewKey]       = $businessProfileResult[$viewKey];
        $result[$impressionKey] = $businessProfileResult[$impressionKey];

        return $result;
    }

    protected function prepareBusinessOverviewReportStats($dates, $rawResult, $dateFormat) : array
    {
        $stats = [];
        $dates = array_flip($dates);

        foreach ($dates as $date => $key) {
            $stats['results'][$date]['date'] = $date;
            $stats['results'][$date][BusinessOverviewModel::TYPE_CODE_VIEW]       = 0;
            $stats['results'][$date][BusinessOverviewModel::TYPE_CODE_IMPRESSION] = 0;
            $stats['results'][$date][self::TYPE_SUM_INTERACTIONS]                 = 0;

            // for chart only
            $stats[BusinessOverviewModel::TYPE_CODE_VIEW][$key]       = 0;
            $stats[BusinessOverviewModel::TYPE_CODE_IMPRESSION][$key] = 0;
        }

        $stats['total'][BusinessOverviewModel::TYPE_CODE_VIEW]       = 0;
        $stats['total'][BusinessOverviewModel::TYPE_CODE_IMPRESSION] = 0;
        $stats['total'][self::TYPE_SUM_INTERACTIONS]                 = 0;

        foreach ($rawResult as $item) {
            $action = $item[BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION];

            if (in_array($action, BusinessOverviewModel::getTypes())) {
                $count    = $item[BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT];
                $datetime = $item[BusinessOverviewReportManager::MONGO_DB_FIELD_DATE_TIME]->toDateTime();

                $viewDate = $datetime->format($dateFormat);

                // for chart only
                if ($action == BusinessOverviewModel::TYPE_CODE_VIEW or $action == BusinessOverviewModel::TYPE_CODE_IMPRESSION) {
                    $stats[$action][$dates[$viewDate]] += $count;
                } else {
                    $action = self::TYPE_SUM_INTERACTIONS;
                }

                // for table
                $stats['results'][$viewDate][$action] += $count;
                $stats['total'][$action] += $count;
            }
        }

        return $stats;
    }
}
