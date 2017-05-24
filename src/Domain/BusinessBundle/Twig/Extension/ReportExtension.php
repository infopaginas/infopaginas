<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 23.09.16
 * Time: 11:09
 */

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Util\DatesUtil;

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
            'ad_usage_allowed_for_business' => new \Twig_Function_Method($this, 'isAdUsageAllowedForBusiness'),
            'convert_monthly_formatted_date' => new \Twig_Function_Method($this, 'convertMonthlyFormattedDate'),
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
