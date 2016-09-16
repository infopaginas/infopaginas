<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 15.09.16
 * Time: 17:03
 */

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;

/**
 * Class InteractionsReportManager
 * @package Domain\ReportBundle\Manager
 */
class InteractionsReportManager
{
    const EVENT_TYPES = [
        'bp_image_click' => 'Image',
        'bp_video_click' => 'Video',
        'bp_map_click' => 'Map',
        'bp_website_click' => 'Website',
        'bp_social_network_click' => 'Social Network',
    ];

    /** @var DataFetcher $gaDataSource */
    protected $gaDataSource;

    /**
     * InteractionsReportManager constructor.
     * @param DataFetcher $gaDataSource
     */
    public function __construct(DataFetcher $gaDataSource)
    {
        $this->gaDataSource = $gaDataSource;
    }

    /**
     * @param array $filterParams
     * @return array
     */
    public function getInteractionsData(array $filterParams = [])
    {
        $dates = $this->getDateRangeVOFromDateString(
            $filterParams['date']['value']['start'],
            $filterParams['date']['value']['end']
        );

        $data = $this->getGaDataSource()->getInteractions($filterParams['businessProfile']['value'], $dates);

        $interactions = [];

        foreach (self::EVENT_TYPES as $key => $category) {
            $interactions[] = [
                'category' => $category,
                'clicks'   => isset($data[$key]) ? $data[$key] : 0,
            ];
        }

        return $interactions;
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
}