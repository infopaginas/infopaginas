<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/12/16
 * Time: 2:28 PM
 */

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Repository\SubscriptionPlanRepository;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\SubscriptionReportSubscription;
use Oxa\Sonata\AdminBundle\Model\Manager\DefaultManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class SubscriptionReportManager extends BaseReportManager
{
    /**
     * @return array|\Domain\BusinessBundle\Entity\SubscriptionPlan[]
     */
    public function getSubscriptionPlans()
    {
        return $this->getEntityManager()
            ->getRepository('DomainBusinessBundle:SubscriptionPlan')
            ->findBy([], ['id' => 'ASC']);
    }

    /**
     * @param SubscriptionReport[] $subscriptionReports
     * @return array|\Domain\BusinessBundle\Entity\SubscriptionPlan[]
     */
    public function getSubscriptionsQuantities(array $subscriptionReports)
    {
        $result = [
            'dates' => [],
            'subscription_quantities' => [],
            'subscription_total_quantities' => [],
            'total_quantity' => 0,
        ];

        $request = $this->container->get('request');
        foreach ($subscriptionReports as $subscriptionReport) {
            $date = $subscriptionReport->getDate()->format(AdminHelper::DATE_FORMAT);
            $result['dates'][] = $date;
            foreach ($subscriptionReport->getSubscriptionReportSubscriptions() as $subscriptionReportSubscription) {
                /** @var SubscriptionReportSubscription $subscriptionReportSubscription*/
                $code = $subscriptionReportSubscription->getSubscriptionPlan()->getCode();

                $subscriptionQuantity = $subscriptionReportSubscription->getQuantity();
                $subscriptionName = $subscriptionReportSubscription
                    ->getSubscriptionPlan()
                    ->getTranslation('name', $request->getLocale());

                $result['subscription_quantities'][$code]['quantities'][] = $subscriptionQuantity;
                $result['subscription_quantities'][$code]['name'] = $subscriptionName;

                if (isset($result['subscription_total_quantities'][$code])) {
                    $result['subscription_total_quantities'][$code]['quantity'] += $subscriptionQuantity;
                } else {
                    $result['subscription_total_quantities'][$code]['quantity'] = $subscriptionQuantity;
                    $result['subscription_total_quantities'][$code]['name'] = $subscriptionName;
                }

                $result['total_quantity'] += $subscriptionQuantity;
            }
        }

        return $result;
    }

    public function saveSubscriptionStats()
    {
        /** @var SubscriptionPlanRepository $repo */
        $repo = $this->getSubscriptionPlanRepository();

        $stats = $repo->getSubscriptionStatistics();

        $date = new \DateTime('today');
        $subscriptionReport = new SubscriptionReport();
        $subscriptionReport->setDate($date);

        $this->getEntityManager()->persist($subscriptionReport);

        foreach ($stats as $subscriptionStat) {
            $subscriptionPlan = $subscriptionStat[0];
            $quantity = $subscriptionStat['cnt'];

            $subscriptionReportSubscription = new SubscriptionReportSubscription();
            $subscriptionReportSubscription->setSubscriptionPlan($subscriptionPlan);
            $subscriptionReportSubscription->setQuantity($quantity);
            $subscriptionReportSubscription->setSubscriptionReport($subscriptionReport);

            $this->getEntityManager()->persist($subscriptionReportSubscription);
        }

        $this->getEntityManager()->flush();
    }

    protected function getSubscriptionPlanRepository() : SubscriptionPlanRepository
    {
        return $this->getEntityManager()->getRepository(SubscriptionPlan::class);
    }
}
