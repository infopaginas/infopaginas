<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Repository\SubscriptionPlanRepository;
use Domain\BusinessBundle\Repository\SubscriptionRepository;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class SubscriptionReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME = 'subscription';

    const MONGO_DB_FIELD_PLAN_CODE = 'code';
    const MONGO_DB_FIELD_COUNT     = 'count';
    const MONGO_DB_FIELD_DATE_TIME = 'datetime';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    /**
     * @return array|\Domain\BusinessBundle\Entity\SubscriptionPlan[]
     */
    public function getSubscriptionPlans()
    {
        return $this->getSubscriptionPlanRepository()->findBy([], ['id' => 'ASC']);
    }

    public function getSubscriptionsReportData(array $params = [])
    {
        $result = [
            'dates'         => [],
            'chart_results' => [],
            'results'       => [],
        ];

        $params['dateObject'] = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['value']['start'],
            $params['date']['value']['end']
        );

        $result['dates'] = DatesUtil::dateRange($params['dateObject']);

        $subscriptionReports = $this->getSubscriptionPlanStats($params);

        $subscriptionStatResult = $this->prepareSubscriptionReportStats($result['dates'], $subscriptionReports);

        $result['results']       = $subscriptionStatResult['results'];
        $result['chart_results'] = $subscriptionStatResult['chart'];
        $result['mapping']       = $subscriptionStatResult['mapping'];

        return $result;
    }

    public function saveSubscriptionStats()
    {
        $stats = $this->getSubscriptionRepository()->getSubscriptionStatistics();
        $data  = $this->buildSubscriptionPlanStats($stats);

        $this->insertSubscriptionPlanStats($data);
    }

    protected function buildSingleSubscriptionPlanStat($code, $count, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_PLAN_CODE   => $code,
            self::MONGO_DB_FIELD_COUNT       => $count,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    protected function buildSubscriptionPlanStats($stats)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(DatesUtil::getYesterday());

        foreach ($stats as $item) {
            $data[] = $this->buildSingleSubscriptionPlanStat($item['code'], $item['cnt'], $date);
        }

        return $data;
    }

    protected function insertSubscriptionPlanStats($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME,
            $data
        );
    }

    public function getSubscriptionPlanStats($params)
    {
        $cursor = $this->mongoDbManager->find(
            self::MONGO_DB_COLLECTION_NAME,
            [
                self::MONGO_DB_FIELD_DATE_TIME => [
                    '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                    '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                ],
            ]
        );

        return $cursor;
    }

    protected function prepareSubscriptionReportStats($dates, $rawResult)
    {
        $subscriptionPlans = $this->getSubscriptionPlans();

        $stats = [];
        $dates = array_flip($dates);

        foreach ($subscriptionPlans as $plan) {
            $code = $plan->getCode();

            $stats['mapping'][$code] = $plan->getName();
        }

        foreach ($dates as $date => $key) {
            $stats['results'][$date]['date'] = $date;

            foreach ($stats['mapping'] as $code => $name) {
                $stats['results'][$date][$code] = 0;
                $stats['chart'][$code][$key]    = 0;
            }

            $stats['results'][$date]['total'] = 0;
        }

        $stats['mapping']['total'] = 'subscription_report.total';

        foreach ($rawResult as $item) {
            $code     = $item[self::MONGO_DB_FIELD_PLAN_CODE];
            $count    = $item[self::MONGO_DB_FIELD_COUNT];
            $datetime = $item[self::MONGO_DB_FIELD_DATE_TIME]->toDateTime();

            $viewDate = $datetime->format(AdminHelper::DATE_FORMAT);

            // for chart
            $stats['chart'][$code][$dates[$viewDate]] += $count;

            // for table
            $stats['results'][$viewDate][$code]       += $count;
            $stats['results'][$viewDate]['total']     += $count;
        }

        return $stats;
    }

    protected function getSubscriptionPlanRepository() : SubscriptionPlanRepository
    {
        return $this->getEntityManager()->getRepository(SubscriptionPlan::class);
    }

    protected function getSubscriptionRepository() : SubscriptionRepository
    {
        return $this->getEntityManager()->getRepository(Subscription::class);
    }
}
