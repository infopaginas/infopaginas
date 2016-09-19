<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.09.16
 * Time: 16:00
 */

namespace Oxa\DfpBundle\Service\Google;


use Oxa\DfpBundle\Google\Api\Ads\Dfp\Lib\DfpUser;

class OrderService
{
    const SERVICE_NAME = 'OrderService';
    const API_VERSION = 'v201605';

    public function __construct(DfpUser $dfpUser)
    {
        $this->dfpUser = $dfpUser;
    }

    public function getCompanyOrderIds(int $companyId)
    {
        $user = $this->getDfpUser();

        $orderService = $user->GetService(self::SERVICE_NAME, self::API_VERSION);

        $statementBuilder = new \StatementBuilder();
        $statementBuilder->Where('advertiserId = ' . $companyId . '');

        $ids = [];

        try {
            $page = $orderService->getOrdersByStatement($statementBuilder->ToStatement());
            $ids = $this->getOrderIdsFromDFPResponse($page);
        } catch (\Exception $e) {
            return $ids;
        }

        return $ids;
    }

    public function getOrderIdsAsAdvertiserPair(array $advertiserIds)
    {
        if (empty($advertiserIds)) {
            return [];
        }
        
        $user = $this->getDfpUser();

        $orderService = $user->GetService(self::SERVICE_NAME, self::API_VERSION);

        array_walk($advertiserIds, function(&$item) {
            $item = '\'' . $item . '\'';
        });

        $statementBuilder = new \StatementBuilder();
        $statementBuilder->Where('advertiserId IN ( ' . implode(',', $advertiserIds) . ' )');

        $ids = [];

        $page = $orderService->getOrdersByStatement($statementBuilder->ToStatement());

        foreach ($page->results as $result) {
            if (isset($result->id)) {
                $ids[$result->id] = $result->advertiserId;
            }
        }

        return $ids;
    }

    protected function getOrderIdsFromDFPResponse(\OrderPage $page)
    {
        $ids = [];

        foreach ($page->results as $order) {
            $ids[] = $order->id;
        }

        return $ids;
    }

    protected function getDfpUser()
    {
        return $this->dfpUser;
    }
}