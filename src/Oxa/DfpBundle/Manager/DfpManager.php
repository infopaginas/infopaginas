<?php

namespace Oxa\DfpBundle\Manager;

use Domain\ReportBundle\Manager\AdUsageReportManager;

/**
 * Class DfpManager
 * @package Oxa\DfpBundle\Manager
 */
class DfpManager
{
    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @var OrderReportManager
     */
    protected $orderReportManager;

    /**
     * @var AdUsageReportManager
     */
    protected $adUsageReportManager;

    public function synchronizeOrderReport($period)
    {
        $reportOrderData = $this->orderReportManager->getOrderReportData($this->getDFPSession(), $period);
        $this->adUsageReportManager->updateAdUsageStats($reportOrderData, $period);
    }

    /**
     * @param AuthManager $authManager
     */
    public function setAuthManager(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * @param OrderReportManager $orderReportManager
     */
    public function setOrderReportManager(OrderReportManager $orderReportManager)
    {
        $this->orderReportManager = $orderReportManager;
    }

    /**
     * @param AdUsageReportManager $adUsageReportManager
     */
    public function setAdUsageReportManager(AdUsageReportManager $adUsageReportManager)
    {
        $this->adUsageReportManager = $adUsageReportManager;
    }

    protected function getDFPSession()
    {
        return $this->authManager->getSession();
    }
}
