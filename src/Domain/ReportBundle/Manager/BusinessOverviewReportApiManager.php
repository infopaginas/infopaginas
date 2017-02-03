<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessOverviewReportApiManager
{
    const API_STATUS_SUCCESS = 'success';
    const API_STATUS_ERROR   = 'error';

    const API_ERROR_ACCESS_DENIED = 'access_denied';
    const API_ERROR_NOT_FOUND     = 'not_found';
    const API_ERROR_INVALID       = 'invalid_params';

    /** @var ContainerInterface $container */
    protected $container;

    /** @var  BusinessProfile $businessProfile */
    protected $businessProfile;

    /** @var  BusinessProfileManager $businessProfileManager */
    protected $businessProfileManager;

    /** @var  BusinessProfileManager $businessOverviewReportManager */
    protected $businessOverviewReportManager;

    /**
     * BusinessOverviewReportApiManager constructor
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->businessProfileManager = $container->get('domain_business.manager.business_profile');

        $this->businessOverviewReportManager =
            $container->get('domain_report.manager.business_overview_report_manager');
    }

    protected function getValidParams()
    {
        return [
            'token',
            'businessSlug',
            'start',
            'end',
        ];
    }

    protected function getResponse($error)
    {
        return [
            'error'  => $error,
            'status' => $error ? self::API_STATUS_ERROR : self::API_STATUS_SUCCESS,
        ];
    }

    protected function prepareReportParameters(array $params)
    {
        $error  = '';

        foreach ($this->getValidParams() as $param) {
            if (!array_key_exists($param, $params)) {
                return [$this->getResponse(self::API_ERROR_INVALID), $params];
            }
        }

        if ($params['token'] != $this->container->getParameter('api_token')) {
            $error = self::API_ERROR_ACCESS_DENIED;
        }

        $businessProfile = $this->businessProfileManager->findBySlug($params['businessSlug']);

        if (!$businessProfile) {
            $error = self::API_ERROR_NOT_FOUND;
        } else {
            $this->businessProfile = $businessProfile;
            $params['businessProfileId'] = $businessProfile->getId();
        }

        $startDate = DatesUtil::isValidDateString($params['start'], DatesUtil::DATE_DB_FORMAT);
        $endDate   = DatesUtil::isValidDateString($params['end'], DatesUtil::DATE_DB_FORMAT);

        if (!$startDate || !$endDate || $startDate > $endDate) {
            $error = self::API_ERROR_INVALID;
        } else {
            $params['date']['start'] = $startDate->format(DatesUtil::START_END_DATE_ARRAY_FORMAT);
            $params['date']['end']   = $endDate->format(DatesUtil::START_END_DATE_ARRAY_FORMAT);
        }

        $result = $this->getResponse($error);

        return [$result, $params];
    }

    /**
     * get business views and impressions in date range
     *
     * @param [] $params
     *
     * @return array
     */
    public function getBusinessViewsAndImpressions($params)
    {
        list($result, $params) = $this->prepareReportParameters($params);

        if (!$result['error']) {
            $result['businessStatus'] = $this->businessProfile->getActiveStatus();
            $result['data']           = [];

            $overviewData = $this->businessOverviewReportManager->getBusinessOverviewData($params);

            foreach ($overviewData['results'] as $data) {
                $result['data'][] = [
                    'date'        => $data['dateObject']->format(DatesUtil::DATE_DB_FORMAT),
                    'views'       => $data['views'],
                    'impressions' => $data['impressions'],
                ];
            }
        }

        return $result;
    }
}
