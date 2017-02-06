<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

/**
 * Class InteractionsReportManager
 * @package Domain\ReportBundle\Manager
 */
class InteractionsReportManager extends BaseReportManager
{
    /** @var  BusinessProfileManager $businessProfileManager */
    protected $businessProfileManager;

    const EVENT_TYPES = [
        BusinessOverviewReport::TYPE_CODE_DIRECTION_BUTTON      => 'interaction_report.button.direction',
        BusinessOverviewReport::TYPE_CODE_MAP_SHOW_BUTTON       => 'interaction_report.button.show_map',
        BusinessOverviewReport::TYPE_CODE_MAP_MARKER_BUTTON     => 'interaction_report.button.marker_map',
        BusinessOverviewReport::TYPE_CODE_WEB_BUTTON            => 'interaction_report.button.web',
        BusinessOverviewReport::TYPE_CODE_CALL_MOB_BUTTON       => 'interaction_report.button.call_mob',
        BusinessOverviewReport::TYPE_CODE_CALL_DESK_BUTTON      => 'interaction_report.button.call_desk',
        BusinessOverviewReport::TYPE_CODE_ADD_COMPARE_BUTTON    => 'interaction_report.button.add_compare',
        BusinessOverviewReport::TYPE_CODE_REMOVE_COMPARE_BUTTON => 'interaction_report.button.remove_compare',
        BusinessOverviewReport::TYPE_CODE_FACEBOOK_SHARE        => 'interaction_report.share.facebook',
        BusinessOverviewReport::TYPE_CODE_TWITTER_SHARE         => 'interaction_report.share.twitter',
        BusinessOverviewReport::TYPE_CODE_FACEBOOK_VISIT        => 'interaction_report.visit.facebook',
        BusinessOverviewReport::TYPE_CODE_TWITTER_VISIT         => 'interaction_report.visit.twitter',
        BusinessOverviewReport::TYPE_CODE_GOOGLE_VISIT          => 'interaction_report.visit.google',
        BusinessOverviewReport::TYPE_CODE_YOUTUBE_VISIT         => 'interaction_report.visit.youtube',
        BusinessOverviewReport::TYPE_CODE_VIDEO_WATCHED         => 'interaction_report.video.watched',
        BusinessOverviewReport::TYPE_CODE_REVIEW_CLICK          => 'interaction_report.review.click',
    ];

    /** @var DataFetcher $gaDataSource */
    protected $gaDataSource;

    /**
     * BusinessOverviewReportManager constructor
     * @param BusinessProfileManager $businessProfileManager
     */
    public function __construct(BusinessProfileManager $businessProfileManager)
    {
        $this->businessProfileManager = $businessProfileManager;
    }

    public function getInteractionsData(array $params = [])
    {
        $businessProfile = $this->getBusinessProfileManager()->find((int)$params['businessProfileId']);

        $businessProfileName = $businessProfile->getTranslation(
            'name',
            $this->getContainer()->getParameter('locale')
        );

        $result = [
            'dates' => [],
            'results' => [],
            'datePeriod' => [
                'start' => $params['date']['start'],
                'end' => $params['date']['end'],
            ],
            'businessProfile' => $businessProfileName
        ];

        $dates = $this->getDateRangeVOFromDateString(
            $params['date']['start'],
            $params['date']['end']
        );

        if (isset($params['periodOption']) && $params['periodOption'] == AdminHelper::PERIOD_OPTION_CODE_PER_MONTH) {
            $dateFormat = AdminHelper::DATE_MONTH_FORMAT;
            $step       = DatesUtil::STEP_MONTH;
        } else {
            $dateFormat = AdminHelper::DATE_FORMAT;
            $step       = DatesUtil::STEP_DAY;
        }

        $result['dates'] = DatesUtil::dateRange($dates, $step, $dateFormat);

        $businessViews = $this->getBusinessProfileInteractions($params);

        $businessProfileResult = $this->prepareBusinessProfileInteractionReportStats(
            $result['dates'],
            $businessViews['results'],
            $dateFormat
        );

        $result['results'] = $businessProfileResult['results'];
        $result['category'] = $businessProfileResult['category'];

        return $result;
    }

    protected function getBusinessProfileInteractions(array $params) : array
    {
        $em = $this->getEntityManager();
        /** @var BusinessOverviewReport[] $businessViews */
        $businessViews = $em->getRepository('DomainReportBundle:BusinessOverviewReport')->getBusinessOverviewReportData(
            $params
        );

        return $businessViews;
    }

    protected function prepareBusinessProfileInteractionReportStats($dates, $views, $dateFormat) : array
    {
        $stats = [];
        $dates = array_flip($dates);

        foreach (self::EVENT_TYPES as $event) {
            $stats['category'][$event] = 0;
        }

        foreach ($dates as $date => $key) {
            $stats['results'][$date] = [
                'date' => $date,
                'dateObject' => \DateTime::createFromFormat($dateFormat, $date),
            ];

            foreach (self::EVENT_TYPES as $event) {
                $stats['results'][$date][$event] = 0;
                $stats[$event][$key] = 0;
            }
        }

        foreach ($views as $view) {
            $viewDate = $view->getDate()->format($dateFormat);

            foreach (self::EVENT_TYPES as $eventKey => $event) {
                $item = $view->{'get' . ucfirst($eventKey)}();

                $stats['results'][$viewDate][$event] += $item;
                $stats[$event][$dates[$viewDate]]    += $item;
            }
        }

        foreach ($stats['results'] as $item) {
            foreach (self::EVENT_TYPES as $eventKey => $event) {
                if (!empty($item[$event])) {
                    $stats['category'][$event] += $item[$event];
                }
            }
        }

        return $stats;
    }

    /**
     * @param string $start
     * @param string $end
     * @return ReportDatesRangeVO
     */
    protected function getDateRangeVOFromDateString(string $start, string $end) : ReportDatesRangeVO
    {
        $startDate = \DateTime::createFromFormat('d-m-Y', $start);
        $endDate   = \DateTime::createFromFormat('d-m-Y', $end);

        return new ReportDatesRangeVO($startDate, $endDate);
    }

    /**
     * @return DataFetcher
     */
    protected function getGaDataSource() : DataFetcher
    {
        return $this->gaDataSource;
    }

    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }
}