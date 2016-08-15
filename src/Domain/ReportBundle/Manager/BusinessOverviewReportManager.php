<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/12/16
 * Time: 2:28 PM
 */

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Entity\BusinessOverviewReportBusinessProfile;
use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Entity\CategoryReportCategory;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\SubscriptionReportSubscription;
use Domain\ReportBundle\Model\BusinessOverviewReportTypeInterface;
use Ivory\CKEditorBundle\Exception\Exception;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class BusinessOverviewReportManager extends DefaultManager
{
    /**
     * @param array $filterParams
     * @return array
     */
    public function getBusinessOverviewDataByFilterParams(array $filterParams = [])
    {
        $params = [];

        if (isset($filterParams['_page'])) {
            $params['page'] = $filterParams['_page'];
        }

        if (isset($filterParams['_per_page'])) {
            $params['perPage'] = $filterParams['_per_page'];
        }

        if (isset($filterParams['date'])) {
            $params['date'] = $filterParams['date']['value'];
        }

        if (isset($filterParams['periodOption'])) {
            $params['periodOption'] = $filterParams['periodOption']['value'];
        }

        $businessKey = 'businessOverviewReportBusinessProfiles__businessProfile';
        if (isset($filterParams[$businessKey]) &&
            $filterParams[$businessKey]['value'] != ''
        ) {
            $params['businessProfileId'] = $filterParams[$businessKey]['value'];
        }

        return $this->getBusinessOverviewData($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getBusinessOverviewData(array $params = [])
    {
        $data = $this->getEntityManager()
            ->getRepository('DomainReportBundle:BusinessOverviewReport')
            ->getBusinessOverviewReportData($params);

        if (isset($params['businessProfileId'])) {
            $businessProfileId = $params['businessProfileId'];

            $businessProfile = $this->getEntityManager()
                ->getRepository('DomainBusinessBundle:BusinessProfile')
                ->findOneBy(['id' => $businessProfileId]);

            $businessProfileName = $businessProfile->getTranslation(
                'name',
                $this->getContainer()->getParameter('locale')
            );
        } else {
            $businessProfileId = null;
            $businessProfileName = null;
        }

        $result = [
            'dates' => [],
            'impressions' => [],
            'views' => [],
            'results' => [],
            'datePeriod' => $data['datePeriod'],
            'businessProfile' => $businessProfileName
        ];

        foreach ($data['results'] as $object) {
            /** @var BusinessOverviewReport $object*/

            // define date format
            if (isset($params['periodOption']) &&
                $params['periodOption'] == AdminHelper::PERIOD_OPTION_CODE_PER_MONTH
            ) {
                $date = $object->getDate()->format(AdminHelper::DATE_MONTH_FORMAT);
            } else {
                $date = $object->getDate()->format(AdminHelper::DATE_FORMAT);
            }

            $impressions = $object->getImpressions($businessProfileId);
            $views       = $object->getViews($businessProfileId);

            if (isset($result['dates'][$date])) {
                // summarize per the same period
                $result['impressions'][$date]  += $impressions;
                $result['views'][$date]        += $views;
            } else {
                $result['impressions'][$date]  = $impressions;
                $result['views'][$date]        = $views;
                $result['dates'][$date]        = $date;
            }

            $result['results'][$date] = [
                'date'          => $result['dates'][$date],
                'impressions'   => $result['impressions'][$date],
                'views'         => $result['views'][$date],
            ];
        }

        // chart requires numbers as keys
        $result['dates']        = array_values($result['dates']);
        $result['views']        = array_values($result['views']);
        $result['impressions']  = array_values($result['impressions']);

        return $result;
    }

    /**
     * @param int $businessProfileId
     * @param \DateTime|null $datetime
     */
    public function registerBusinessView(int $businessProfileId, \DateTime $datetime = null)
    {
        $this->registerBusinessOverview(
            BusinessOverviewReportTypeInterface::TYPE_CODE_VIEW,
            $businessProfileId,
            $datetime
        );
    }

    /**
     * @param int $businessProfileId
     * @param \DateTime|null $datetime
     */
    public function registerBusinessImpression(int $businessProfileId, \DateTime $datetime = null)
    {
        $this->registerBusinessOverview(
            BusinessOverviewReportTypeInterface::TYPE_CODE_IMPRESSION,
            $businessProfileId,
            $datetime
        );
    }

    /**
     * @param $type
     * @param int $businessProfileId
     * @param \DateTime|null $datetime
     */
    private function registerBusinessOverview($type, int $businessProfileId, \DateTime $datetime = null)
    {
        if (!array_key_exists($type, BusinessOverviewReportBusinessProfile::getTypes())) {
            throw new \InvalidArgumentException(sprintf('Invalid Business overview report type (%s)'), $type);
        }

        $em = $this->getEntityManager();

        $businessProfile = $em->getRepository('DomainBusinessBundle:BusinessProfile')->findOneBy([
            'id' => $businessProfileId
        ]);

        if (!$businessProfile) {
            throw new \InvalidArgumentException(sprintf('Invalid Business profile Id (%s)'), $businessProfileId);
        }

        $datetime = ($datetime) ? $datetime : new \DateTime();

        $businessOverviewReport = $em->getRepository('DomainReportBundle:BusinessOverviewReport')->findOneBy([
            'date' => $datetime
        ]);

        if (!$businessOverviewReport) {
            $businessOverviewReport = new BusinessOverviewReport();
            $businessOverviewReport->setDate($datetime);
        }

        $businessOverviewReportBusinessProfile = new BusinessOverviewReportBusinessProfile();
        $businessOverviewReportBusinessProfile->setBusinessOverviewReport($businessOverviewReport);
        $businessOverviewReportBusinessProfile->setBusinessProfile($businessProfile);
        $businessOverviewReportBusinessProfile->setDatetime($datetime);
        $businessOverviewReportBusinessProfile->setType($type);

        $em->persist($businessOverviewReport);
        $em->persist($businessOverviewReportBusinessProfile);

        $em->flush();
    }

    /**
     * @param array $filterParams
     * @param string $format
     * @return mixed
     */
    public function getBusinessOverviewReportDataAndName(array $filterParams, string $format) : array
    {
        $businessOverviewData = $this->getBusinessOverviewDataByFilterParams($filterParams);

        if ($businessOverviewData['businessProfile']) {
            $reportName = str_replace(' ', '_', $businessOverviewData['businessProfile']);
        } else {
            $reportName = 'business_overview_report';
        }

        $filename = $this->generateReportName($format, $reportName);

        return [$businessOverviewData, $filename];
    }
}
