<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.09.16
 * Time: 16:52
 */

namespace Oxa\DfpBundle\Service\Google;

use Oxa\DfpBundle\Google\Api\Ads\Dfp\Lib\DfpUser;

/**
 * Class LineItemService
 * @package Oxa\DfpBundle\Service\Google
 */
class LineItemService
{
    const SERVICE_NAME = 'LineItemService';
    const API_VERSION = 'v201605';

    /**
     * LineItemService constructor.
     * @param DfpUser $dfpUser
     */
    public function __construct(DfpUser $dfpUser)
    {
        $this->dfpUser = $dfpUser;
    }

    /**
     * @param array $orderIds
     * @return array
     */
    public function getLineItemIdsByOrderIds(array $orderIds)
    {
        $user = $this->getDfpUser();

        $lineItemService = $user->GetService(self::SERVICE_NAME, self::API_VERSION);

        $statementBuilder = new \StatementBuilder();
        $statementBuilder->Where('orderId IN (' . implode(', ', $orderIds). ')');

        $page = $lineItemService->getLineItemsByStatement($statementBuilder->ToStatement());

        $ids = [];

        foreach ($page->results as $lineItem) {
            $ids[] = $lineItem->id;
        }

        return $ids;
    }

    /**
     * @param array $orderIds
     * @return array
     */
    public function getLineItemIdsAsOrderPair(array $orderIds)
    {
        $user = $this->getDfpUser();

        $lineItemService = $user->GetService(self::SERVICE_NAME, self::API_VERSION);

        $statementBuilder = new \StatementBuilder();
        $statementBuilder->Where('orderId IN (' . implode(', ', $orderIds). ')');

        $page = $lineItemService->getLineItemsByStatement($statementBuilder->ToStatement());

        $ids = [];

        foreach ($page->results as $lineItem) {
            if (isset($lineItem->id)) {
                $ids[$lineItem->id] = $lineItem->orderId;
            }
        }

        return $ids;
    }

    /**
     * @return DfpUser
     */
    protected function getDfpUser()
    {
        return $this->dfpUser;
    }
}