<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\DfpBundle\Manager\OrderReportManager;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class AdUsageReportManager
 * @package Domain\ReportBundle\Manager
 */
class AdUsageReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME   = 'dc_order_stat';

    const MONGO_DB_FIELD_DEVICE_CATEGORY_NAME   = 'device_category_name';
    const MONGO_DB_FIELD_DEVICE_CATEGORY_ID     = 'device_category_id';
    const MONGO_DB_FIELD_ORDER_ID               = 'order_id';
    const MONGO_DB_FIELD_CLICKS                 = 'clicks';
    const MONGO_DB_FIELD_IMPRESSIONS            = 'impressions';
    const MONGO_DB_FIELD_CTR                    = 'ctr';
    const MONGO_DB_FIELD_DATE_TIME              = 'datetime';

    const DEVICE_CATEGORY_ID_DESKTOP        = 30000;
    const DEVICE_CATEGORY_ID_SMART_PHONE    = 30001;
    const DEVICE_CATEGORY_ID_TABLET         = 30002;

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    protected $reportName = 'ad_usage_report';

    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    public function getAdUsageData(array $params = [])
    {
        $result = [
            'chart'   => [],
            'results' => [],
            'total'   => [],
            'deviceCategories' => self::getDeviceList(),
        ];

        /* @var BusinessProfile $businessProfile */
        $businessProfile = $params['businessProfile'];

        $params['dateObject'] = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['start'],
            $params['date']['end']
        );

        $result['dates'] = DatesUtil::dateRange($params['dateObject'], DatesUtil::STEP_DAY, AdminHelper::DATE_FORMAT);

        $params['orderId'] = $businessProfile->getDCOrderId();

        $adUsageData = $this->getAdUsageStats($params);

        $adUsageResult = $this->prepareAdUsageReportStats($result['dates'], $adUsageData);

        $result['results'] = $adUsageResult['results'];
        $result['chart']   = $adUsageResult['chart'];
        $result['total']   = $adUsageResult['total'];

        return $result;
    }

    protected function prepareAdUsageReportStats($dates, $rawResult)
    {
        $stat = [];

        $dates = array_flip($dates);

        $deviceList = self::getDeviceList();

        foreach ($dates as $date => $key) {
            $stat['chart'][self::MONGO_DB_FIELD_CLICKS][$key]      = 0;
            $stat['chart'][self::MONGO_DB_FIELD_IMPRESSIONS][$key] = 0;

            foreach ($deviceList as $deviceKey => $device) {
                $stat['results'][$deviceKey][$key] = [
                    'date'           => $date,
                    'deviceCategory' => $device,
                    'impressions'    => 0,
                    'clicks'         => 0,
                    'ctr'            => 0,
                ];
            }
        }

        $stat['total'][self::MONGO_DB_FIELD_CLICKS] = 0;
        $stat['total'][self::MONGO_DB_FIELD_IMPRESSIONS] = 0;

        foreach ($rawResult as $item) {
            $deviceCategoryId = $item[self::MONGO_DB_FIELD_DEVICE_CATEGORY_ID];
            $datetime = $item[self::MONGO_DB_FIELD_DATE_TIME]->toDateTime();

            $click       = $item[self::MONGO_DB_FIELD_CLICKS];
            $impressions = $item[self::MONGO_DB_FIELD_IMPRESSIONS];
            $ctr         = $item[self::MONGO_DB_FIELD_CTR];

            $viewDate = $datetime->format(AdminHelper::DATE_FORMAT);

            $stat['results'][$deviceCategoryId][$dates[$viewDate]] = [
                'date'           => $viewDate,
                'deviceCategory' => $deviceList[$deviceCategoryId],
                'clicks'         => $click,
                'impressions'    => $impressions,
                'ctr'            => $ctr,
            ];

            $stat['chart'][self::MONGO_DB_FIELD_CLICKS][$dates[$viewDate]]      += $click;
            $stat['chart'][self::MONGO_DB_FIELD_IMPRESSIONS][$dates[$viewDate]] += $impressions;

            $stat['total'][self::MONGO_DB_FIELD_CLICKS]      += $click;
            $stat['total'][self::MONGO_DB_FIELD_IMPRESSIONS] += $impressions;
        }

        return $stat;
    }

    protected function getAdUsageStats($params)
    {
        $cursor = $this->mongoDbManager->find(
            self::MONGO_DB_COLLECTION_NAME,
            [
                self::MONGO_DB_FIELD_ORDER_ID => $params['orderId'],
                self::MONGO_DB_FIELD_DATE_TIME => [
                    '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                    '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                ],
            ]
        );

        return $cursor;
    }

    public function updateAdUsageStats($reportData, $period)
    {
        $data = $this->buildOrderStats($reportData, $period);

        $this->removeOldOrderStats($period);
        $this->insertOrderStats($data);
    }

    protected function insertOrderStats($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME,
            $data
        );
    }

    protected function removeOldOrderStats($period)
    {
        $reportRange  = DatesUtil::getAdUsageReportRangeByPeriod($period);
        $reportPeriod = DatesUtil::getDateRangeValueObjectFromRangeType($reportRange);

        $this->mongoDbManager->deleteMany(
            self::MONGO_DB_COLLECTION_NAME,
            [
                self::MONGO_DB_FIELD_DATE_TIME => [
                    '$gte' => $this->mongoDbManager->typeUTCDateTime($reportPeriod->getStartDate()),
                    '$lte' => $this->mongoDbManager->typeUTCDateTime($reportPeriod->getEndDate()),
                ],
            ]
        );
    }

    protected function buildOrderStats($reportData, $period)
    {
        $data = [];

        $reportDate = DatesUtil::getAdUsageReportDateByPeriod($period);

        $date = $this->mongoDbManager->typeUTCDateTime($reportDate);

        foreach ($reportData as $order) {
            $data[] = $this->buildOrderStat($order, $date);
        }

        return $data;
    }

    protected function buildOrderStat($dcOrderData, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_ORDER_ID               => $dcOrderData[OrderReportManager::DIMENSION_ORDER_ID],
            self::MONGO_DB_FIELD_DEVICE_CATEGORY_NAME   => $dcOrderData[OrderReportManager::DIMENSION_DEVICE_CATEGORY_NAME],
            self::MONGO_DB_FIELD_DEVICE_CATEGORY_ID     => $dcOrderData[OrderReportManager::DIMENSION_DEVICE_CATEGORY_ID],
            self::MONGO_DB_FIELD_CLICKS                 => (int)$dcOrderData[OrderReportManager::COLUMN_CLICKS],
            self::MONGO_DB_FIELD_IMPRESSIONS            => (int)$dcOrderData[OrderReportManager::COLUMN_IMPRESSIONS],
            self::MONGO_DB_FIELD_CTR                    => (float)$dcOrderData[OrderReportManager::COLUMN_CTR],
            self::MONGO_DB_FIELD_DATE_TIME              => $date,
        ];

        return $data;
    }

    public static function getDeviceList()
    {
        return [
            self::DEVICE_CATEGORY_ID_DESKTOP        => 'device_category_desktop',
            self::DEVICE_CATEGORY_ID_SMART_PHONE    => 'device_category_smart_phone',
            self::DEVICE_CATEGORY_ID_TABLET         => 'device_category_tablet',
        ];
    }
}