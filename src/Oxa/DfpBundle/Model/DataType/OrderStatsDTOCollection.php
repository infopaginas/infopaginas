<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 27.08.16
 * Time: 12:56
 */

namespace Oxa\DfpBundle\Model\DataType;

/**
 * Class OrderStatsDTOCollection
 * @package Oxa\DfpBundle\Model\DataType
 */
class OrderStatsDTOCollection
{
    /** @var array of OrderStatsDTO */
    protected $statsDTOs;

    /**
     * OrderStatsDTOCollection constructor.
     * @param array $stats
     */
    public function __construct(array $stats)
    {
        foreach ($stats as $orderId => $orderInfo) {
            $dto = new OrderStatsDTO($orderId, $orderInfo['clicks'], $orderInfo['impressions']);
            $this->statsDTOs[] = $dto;
        }
    }

    /**
     * @return array
     */
    public function getStats() : array
    {
        return $this->statsDTOs;
    }
}