<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 17.09.16
 * Time: 16:12
 */

namespace Domain\ReportBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Oxa\DfpBundle\Entity\DoubleClickLineItem;
use Oxa\DfpBundle\Manager\DfpManager;
use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class AdUsageReportManager
 * @package Domain\ReportBundle\Manager
 */
class AdUsageReportManager
{
    const CACHE_LIFETIME = 3600;

    protected $entityManager;

    protected $dfpManager;

    protected $cacheService;

    public function __construct(EntityManagerInterface $entityManager, DfpManager $dfpManager, CacheItemPoolInterface $cacheService)
    {
        $this->entityManager = $entityManager;
        $this->dfpManager = $dfpManager;
        $this->cacheService = $cacheService;
    }

    public function getAdUsageData(array $params = [])
    {
        $businessProfileId = $params['businessProfileId'];

        $item = $this->loadDataFromCache(
            $businessProfileId,
            $params['date']['start'],
            $params['date']['end']
        );

        if (!$item->isHit()) {
            $businessProfile = $this->getEntityManager()->getRepository(BusinessProfile::class)
                ->find($businessProfileId);

            $lineItemIds = $this->getEntityManager()->getRepository(DoubleClickLineItem::class)
                ->getLineItemIdsByBusinessProfile($businessProfile);

            $dateRange = $this->getDateRangeVOFromDateString($params['date']['start'], $params['date']['end']);

            $stats = $this->getDfpManager()->getStatsForMultipleLineItems($lineItemIds, $dateRange);

            $this->saveDataToCache($item, $stats);

            return $stats;
        }

        return $item->get();
    }

    protected function getDateRangeVOFromDateString(string $start, string $end) : DateRangeVO
    {
        $startDate = \DateTime::createFromFormat('d-m-Y', $start);
        $endDate = \DateTime::createFromFormat('d-m-Y', $end);

        return new DateRangeVO($startDate, $endDate);
    }

    protected function getEntityManager() : EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getDfpManager() : DfpManager
    {
        return $this->dfpManager;
    }

    protected function getCacheService() : CacheItemPoolInterface
    {
        return $this->cacheService;
    }

    /**
     * @param string $businessProfileSlug
     * @param string $startDate
     * @param string $endDate
     * @return CacheItemInterface
     */
    protected function loadDataFromCache(
        string $businessProfileSlug,
        string $startDate,
        string $endDate
    ) : CacheItemInterface {
        $cacheKey = sha1($businessProfileSlug . $startDate . '-' . $endDate);
        return $this->getCacheService()->getItem($cacheKey);
    }

    /**
     * @param CacheItemInterface $item
     * @param $data
     */
    protected function saveDataToCache(CacheItemInterface $item, $data)
    {
        $item->set($data)->expiresAfter(self::CACHE_LIFETIME);
        $this->getCacheService()->save($item);
    }
}