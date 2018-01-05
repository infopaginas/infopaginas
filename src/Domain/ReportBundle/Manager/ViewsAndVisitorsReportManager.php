<?php

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class ViewsAndVisitorsReportManager
 * @package Domain\ReportBundle\Manager
 */
class ViewsAndVisitorsReportManager extends BaseReportManager
{
    const TYPE_SUM_INTERACTIONS = 'interactions';

    /** @var BusinessOverviewReportManager $businessOverviewReportManager */
    protected $businessOverviewReportManager;

    protected $reportName = 'view_and_visitors_report';

    /**
     * @param BusinessOverviewReportManager $businessOverviewReportManager
     */
    public function __construct(BusinessOverviewReportManager $businessOverviewReportManager)
    {
        $this->businessOverviewReportManager = $businessOverviewReportManager;
    }

    /**
     * @param array $params
     *
     * @return array
     */
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

        $periodOption = !empty($params['periodOption']['value']) ? $params['periodOption']['value'] : '';

        list($dateFormat, $step) = $this->handlePeriodOption($periodOption);

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
        $result['mapping'] = $businessProfileResult['mapping'];

        $viewKey       = BusinessOverviewModel::TYPE_CODE_VIEW;
        $impressionKey = BusinessOverviewModel::TYPE_CODE_IMPRESSION;

        $result[$viewKey]       = $businessProfileResult[$viewKey];
        $result[$impressionKey] = $businessProfileResult[$impressionKey];

        return $result;
    }

    /**
     * @param array     $dates
     * @param mixed     $rawResult
     * @param string    $dateFormat
     *
     * @return array
     */
    protected function prepareBusinessOverviewReportStats($dates, $rawResult, $dateFormat) : array
    {
        $stats = [];
        $dates = array_flip($dates);

        $stats['mapping'] = [
            BusinessOverviewModel::TYPE_CODE_VIEW       => 'interaction_report.event.view',
            BusinessOverviewModel::TYPE_CODE_IMPRESSION => 'interaction_report.event.impression',
            self::TYPE_SUM_INTERACTIONS                 => 'interaction_report.sum.interaction',
        ];

        foreach ($dates as $date => $key) {
            $stats['results'][$date]['date'] = $date;

            foreach ($stats['mapping'] as $code => $name) {
                $stats['results'][$date][$code] = 0;
            }

            // for chart only
            $stats[BusinessOverviewModel::TYPE_CODE_VIEW][$key]       = 0;
            $stats[BusinessOverviewModel::TYPE_CODE_IMPRESSION][$key] = 0;
        }

        foreach ($stats['mapping'] as $code => $name) {
            $stats['total'][$code] = 0;
        }

        foreach ($rawResult as $item) {
            $action = $item[BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION];

            if (in_array($action, BusinessOverviewModel::getTypes())) {
                $count    = $item[BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT];
                $datetime = DatesUtil::convertMongoDbTimeToDatetime(
                    $item[BusinessOverviewReportManager::MONGO_DB_FIELD_DATE_TIME]
                );

                if ($dateFormat == AdminHelper::DATE_WEEK_FORMAT) {
                    $viewDate = DatesUtil::getWeeklyFormatterDate($datetime);
                } else {
                    $viewDate = $datetime->format($dateFormat);
                }

                // for chart only
                if ($action == BusinessOverviewModel::TYPE_CODE_VIEW or
                    $action == BusinessOverviewModel::TYPE_CODE_IMPRESSION
                ) {
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
