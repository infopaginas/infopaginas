<?php

namespace Oxa\DfpBundle\Controller;

use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /*$orderId = 350347287;
        $dateRange = new DateRangeVO(new \DateTime('-7 days'), new \DateTime());

        $dfpManager = $this->get('oxa_dfp.manager');

        //$stats = $dfpManager->getStatsForMultipleOrders([$orderId, 350324727], $dateRange);

        $stats = $dfpManager->getStatsForSingleOrder($orderId, $dateRange);
        dump($stats);
        die();*/

        $advertiserId = 'oxagile';

        $companyService = $this->get('oxa_dfp.service.company');
        $companyId = $companyService->getAdvertiserIdByExternalIdAttr($advertiserId);

        $orderService = $this->get('oxa_dfp.service.order');
        $companyOrdersIds = $orderService->getCompanyOrderIds($companyId);

        $lineItemService = $this->get('oxa_dfp.service.line_item');
        $lineItemIds = $lineItemService->getLineItemIdsByOrderIds($companyOrdersIds);

        $dfpManager = $this->get('oxa_dfp.manager');
        $dateRange = new DateRangeVO(new \DateTime('-30 days'), new \DateTime());

        $companyOrderStats = $dfpManager->getStatsForMultipleOrders($lineItemIds, $dateRange);

        dump($companyOrderStats);
        die();
    }
}
