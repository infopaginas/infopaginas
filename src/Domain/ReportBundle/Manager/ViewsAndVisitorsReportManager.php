<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 15.09.16
 * Time: 12:10
 */

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class ViewsAndVisitorsReportManager
 * @package Domain\ReportBundle\Manager
 */
class ViewsAndVisitorsReportManager
{
    /** @var DataFetcher $gaDataSource */
    protected $gaDataSource;

    public function __construct(DataFetcher $gaDataSource)
    {
        $this->gaDataSource = $gaDataSource;
    }

    public function getViewsAndVisitorsData(array $filterParams = [])
    {
        $dates = $this->getDateRangeVOFromDateString(
            $filterParams['date']['value']['start'],
            $filterParams['date']['value']['end']
        );

        if (isset($filterParams['periodOption']) &&
            $filterParams['periodOption']['value'] == AdminHelper::PERIOD_OPTION_CODE_PER_MONTH
        ) {
            $dimension = 'yearMonth';
        } else {
            $dimension = 'date';
        }

        $data = $this->getGaDataSource()->getWebsiteViewsAndVisitors($dates, $dimension);

        $result['results'] = $data;

        $result['views'] = array_values(array_map(function($item) {
            return $item['views'];
        }, $data));

        $result['visitors'] = array_values(array_map(function($item) {
            return $item['visitors'];
        }, $data));

        $result['dates'] = array_keys($data);

        return $result;
    }

    protected function getDateRangeVOFromDateString(string $start, string $end) : ReportDatesRangeVO
    {
        $startDate = \DateTime::createFromFormat('d-m-Y', $start);
        $endDate   = \DateTime::createFromFormat('d-m-Y', $end);

        return new ReportDatesRangeVO($startDate, $endDate);
    }

    protected function getGaDataSource() : DataFetcher
    {
        return $this->gaDataSource;
    }
}
