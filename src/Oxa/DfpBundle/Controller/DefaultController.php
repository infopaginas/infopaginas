<?php

namespace Oxa\DfpBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\DfpBundle\Entity\DoubleClickLineItem;
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

        /*$advertiserId = 'oxagile';

        $companyService = $this->get('oxa_dfp.manager.doubleclick_companies');

        $companyService->synchronizeBusinessProfilesDoubleClickCompanies();*/


        /*$companyId = $companyService->getAdvertiserIdByExternalIdAttr($advertiserId);

        $orderService = $this->get('oxa_dfp.service.order');
        $companyOrdersIds = $orderService->getCompanyOrderIds($companyId);

        $lineItemService = $this->get('oxa_dfp.service.line_item');
        $lineItemIds = $lineItemService->getLineItemIdsByOrderIds($companyOrdersIds);

        $dfpManager = $this->get('oxa_dfp.manager');
        $dateRange = new DateRangeVO(new \DateTime('-30 days'), new \DateTime());

        $companyOrderStats = $dfpManager->getStatsForMultipleOrders($lineItemIds, $dateRange);

        dump($companyOrderStats);
        die();*/

        /*$ordersManager = $this->get('oxa_dfp.manager.doubleclick_orders');
        $ordersManager->synchronizeDoubleClickOrders();*/

        $businessProfile = $this->getDoctrine()->getRepository(BusinessProfile::class)->find(18);
        $lineItemIds = $this->getDoctrine()->getRepository(DoubleClickLineItem::class)
            ->getLineItemIdsByBusinessProfile($businessProfile);

        $dfpManager = $this->get('oxa_dfp.manager');
        $dateRange = new DateRangeVO(new \DateTime('-30 days'), new \DateTime());

        $stats = $dfpManager->getStatsForMultipleLineItems($lineItemIds, $dateRange);
        dump($stats);
        die();
    }
}
