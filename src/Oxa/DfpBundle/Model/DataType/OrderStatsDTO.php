<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 25.08.16
 * Time: 22:21
 */

namespace Oxa\DfpBundle\Model\DataType;

/**
 * Class OrderStatsDTO
 * @package Oxa\DfpBundle\Model\DataType
 */
class OrderStatsDTO
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var int
     */
    private $clicks;

    /**
     * @var int
     */
    private $impressions;

    /**
     * OrderStatsDTO constructor.
     * @param int $orderId
     * @param int $clicks
     * @param int $impressions
     */
    public function __construct(int $orderId, int $clicks, int $impressions)
    {
        $this->orderId     = $orderId;
        $this->clicks      = $clicks;
        $this->impressions = $impressions;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}