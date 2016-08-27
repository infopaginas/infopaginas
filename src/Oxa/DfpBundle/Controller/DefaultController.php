<?php

namespace Oxa\DfpBundle\Controller;

use Oxa\DfpBundle\Model\DataType\DateRangeVO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $orderId = 350347287;
        $dateRange = new DateRangeVO(new \DateTime('-7 days'), new \DateTime());

        $dfpManager = $this->get('oxa_dfp.manager');

        //$stats = $dfpManager->getStatsForMultipleOrders([$orderId, 350324727], $dateRange);

        $stats = $dfpManager->getStatsForSingleOrder($orderId, $dateRange);
        dump($stats);
        die();
    }
}
