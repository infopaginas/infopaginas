<?php

namespace Domain\ReportBundle\Google\Analytics;

use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class DataFetcher
 * @package Domain\ReportBundle\Google\Analytics
 */
class DataFetcher extends \Happyr\GoogleAnalyticsBundle\Service\DataFetcher
{
    const DEFAULT_DATE_RANGE_FROM = '-1 year';
    const DEFAULT_DATE_RANGE_TO = 'now';

    const GA_DATE_FORMAT = 'Y-m-d';

    /**
     * @var \Google_Service_Analytics $analytics
     */
    protected $analytics;

    /** @var ClientProvider $clientProvider */
    protected $clientProvider;

    /**
     * DataFetcher constructor.
     *
     * @param CacheItemPoolInterface $cache
     * @param \Google_Client $client
     * @param int $viewId
     * @param int $cacheLifetime
     */
    public function __construct(CacheItemPoolInterface $cache, \Google_Client $client, $viewId, $cacheLifetime)
    {
        parent::__construct($cache, $client, $viewId, $cacheLifetime);
    }

    /**
     * @param string $uri
     * @param ReportDatesRangeVO $datesRange
     * @param string $dimension
     * @param string $regex
     * @return mixed
     * @throws \Exception
     */
    public function getViews($uri, ReportDatesRangeVO $datesRange, string $dimension = 'date', $regex = '$')
    {
        $start = $this->getFormattedStartDate($datesRange->getStartDate());
        $end   = $this->getFormattedEndDate($datesRange->getEndDate());

        $item = $this->loadDataFromCache($uri, $start, $end, $dimension);

        if (!$item->isHit()) {
            $gaId    = 'ga:' . $this->viewId;
            $metrics = 'ga:pageviews';

            $filters = [
                'filters' => 'ga:pagePath=~' . $uri . $regex,
                'dimensions' => 'ga:' . $dimension,
            ];

            $data     = $this->getAnalyticsDataResource()->get($gaId, $start, $end, $metrics, $filters);
            $prepared = $this->prepareResults($data);

            $this->saveDataToCache($item, $prepared);

            return $prepared;
        }

        return $item->get();
    }

    /**
     * @param $productId
     * @param ReportDatesRangeVO $datesRangeVO
     * @param string $dimension
     * @return mixed
     * @throws \Exception
     */
    public function getImpressions($productId, ReportDatesRangeVO $datesRangeVO, string $dimension = 'date')
    {
        $start = $this->getFormattedStartDate($datesRangeVO->getStartDate());
        $end   = $this->getFormattedEndDate($datesRangeVO->getEndDate());

        $item = $this->loadDataFromCache($productId, $start, $end, $dimension);

        if (!$item->isHit()) {
            $gaId    = 'ga:' . $this->viewId;
            $metrics = 'ga:productListViews';

            $filters = [
                'filters' => 'ga:productSku=~' . $productId,
                'dimensions' => 'ga:' . $dimension,
            ];

            $data     = $this->getAnalyticsDataResource()->get($gaId, $start, $end, $metrics, $filters);
            $prepared = $this->prepareResults($data);

            $this->saveDataToCache($item, $prepared);

            return $prepared;
        }

        return $item->get();
    }

    /**
     * @param ReportDatesRangeVO $datesRange
     * @param string $dimension
     * @return array
     */
    public function getWebsiteViewsAndVisitors(ReportDatesRangeVO $datesRange, string $dimension = 'date')
    {
        $start = $this->getFormattedStartDate($datesRange->getStartDate());
        $end   = $this->getFormattedEndDate($datesRange->getEndDate());

        $uri = '/$';

        $gaId    = 'ga:' . $this->viewId;
        $metrics = 'ga:pageviews,ga:uniquePageviews';

        $filters = [
            'filters' => 'ga:dimension1=~USER;ga:pagePath=~' . $uri,
            'dimensions' => 'ga:' . $dimension,
        ];

        $data = $this->getAnalyticsDataResource()->get($gaId, $start, $end, $metrics, $filters);

        $prepared = [];

        foreach ($this->prepareResults($data) as $item) {
            if ($dimension == 'date') {
                $date = \DateTime::createFromFormat('Ymd', $item[0]);
                $format = DefaultManager::REPORT_ADMIN_DATE_FORMAT;
            } else {
                $date = \DateTime::createFromFormat('Ym', $item[0]);
                $format = 'm.Y';
            }

            $prepared[$date->format($format)] = [
                'views' => $item[1],
                'visitors' => $item[2],
            ];
        }

        return $prepared;
    }

    public function getInteractions($eventLabel, ReportDatesRangeVO $datesRange)
    {
        $start = $this->getFormattedStartDate($datesRange->getStartDate());
        $end   = $this->getFormattedEndDate($datesRange->getEndDate());

        $gaId    = 'ga:' . $this->viewId;
        $metrics = 'ga:totalEvents';

        $filters = [
            'filters' => 'ga:eventCategory=~interaction;ga:eventLabel=~' . $eventLabel,
            'dimensions' => 'ga:eventCategory,ga:eventLabel,ga:eventAction',
        ];

        $data = $this->getAnalyticsDataResource()->get($gaId, $start, $end, $metrics, $filters);

        $prepared = [];

        foreach ($this->prepareResults($data) as $item) {
            $prepared[$item[2]] = $item[3];
        }

        return $prepared;
    }

    /**
     * @param ClientProvider $clientProvider
     */
    public function setClientProvider(ClientProvider $clientProvider)
    {
        $this->clientProvider = $clientProvider;
        $this->reInitClient();
    }

    /**
     *
     */
    protected function reInitClient()
    {
        $this->client    = $this->clientProvider->getClient();
        $this->analytics = new \Google_Service_Analytics($this->client);
    }

    /**
     * @param \Google_Service_Analytics_GaData $data
     * @return mixed
     */
    protected function prepareResults(\Google_Service_Analytics_GaData $data)
    {
        $rows = $data->getRows();
        return $rows === null ? [] : $rows;
    }

    /**
     * @param \DateTime $startDate
     * @return string
     */
    protected function getFormattedStartDate(\DateTime $startDate)
    {
        if ($startDate === null) {
            $startDate = new \DateTime(self::DEFAULT_DATE_RANGE_FROM);
        }

        return $startDate->format(self::GA_DATE_FORMAT);
    }

    /**
     * @param \DateTime $endDate
     * @return string
     */
    protected function getFormattedEndDate(\DateTime $endDate)
    {
        if ($endDate === null) {
            $endDate = new \DateTime(self::DEFAULT_DATE_RANGE_TO);
        }

        return $endDate->format(self::GA_DATE_FORMAT);
    }

    /**
     * @param string $uri
     * @param string $startDate
     * @param string $endDate
     * @param string $dimension
     * @return CacheItemInterface
     */
    protected function loadDataFromCache(
        string $uri,
        string $startDate,
        string $endDate,
        string $dimension
    ) : CacheItemInterface {
        $cacheKey = sha1($uri . $startDate . '-' . $endDate . '-' . $dimension);
        return $this->cache->getItem($cacheKey);
    }

    /**
     * @param CacheItemInterface $item
     * @param $data
     */
    protected function saveDataToCache(CacheItemInterface $item, $data)
    {
        $item->set($data)->expiresAfter($this->cacheLifetime);
        $this->cache->save($item);
    }

    /**
     * @return \Google_Service_Analytics_DataGa_Resource
     */
    protected function getAnalyticsDataResource()
    {
        return $this->analytics->data_ga;
    }
}
