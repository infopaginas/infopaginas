<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Util\DatesUtil;
use Twig\TwigFunction;

/**
 * Class ReportExtension
 * @package Domain\BusinessBundle\Twig\Extension
 */
class ReportExtension extends \Twig_Extension
{
    /** @var BusinessProfileManager */
    private $businessProfileManager;

    /**
     * @param BusinessProfileManager $businessProfileManager
     */
    public function setBusinessProfileManager(BusinessProfileManager $businessProfileManager)
    {
        $this->businessProfileManager = $businessProfileManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'ad_usage_allowed_for_business'  => new TwigFunction($this, 'isAdUsageAllowedForBusiness'),
            'convert_monthly_formatted_date' => new TwigFunction($this, 'convertMonthlyFormattedDate'),
            'get_events_with_priority'       => new TwigFunction($this, 'getEventsWithPriority'),
            'get_month_range_by_period'      => new TwigFunction($this, 'getMothRangeByPeriod'),
        ];
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return int
     */
    public function isAdUsageAllowedForBusiness(BusinessProfile $businessProfile)
    {
        return $this->getBusinessProfileManager()->isAdUsageReportAllowedForBusiness($businessProfile);
    }

    public function convertMonthlyFormattedDate($date, $format)
    {
        return DatesUtil::convertMonthlyFormattedDate($date, $format);
    }

    /**
     * @param string $priority
     *
     * @return array
     */
    public function getEventsWithPriority($priority)
    {
        $eventsByPriority = BusinessOverviewModel::getEventTypesByPriority();

        if (!empty($eventsByPriority[$priority])) {
            $events = $eventsByPriority[$priority];
        } else {
            $events = [];
        }

        return $events;
    }

    /**
     * @param string $period
     *
     * @return int
     */
    public function getMothRangeByPeriod($period)
    {
        switch ($period) {
            case DatesUtil::RANGE_LAST_MONTH:
                $month = 1;
                break;
            case DatesUtil::RANGE_LAST_3_MONTH:
                $month = 3;
                break;
            case DatesUtil::RANGE_LAST_6_MONTH:
                $month = 6;
                break;
            case DatesUtil::RANGE_LAST_12_MONTH:
                $month = 12;
                break;
            default:
                $month = 0;
                break;
        }

        return $month;
    }

    /**
     * @return BusinessProfileManager
     */
    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'report_extension';
    }
}
