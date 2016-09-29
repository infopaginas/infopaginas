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
    public function getSubscriptionsQuantities(array $subscriptionReports, $dates = [], $subscriptionPlans)
    {
        $result = [
            'dates' => $dates,
            'subscription_quantities' => [],
            'subscription_total_quantities' => [],
            'total_quantity' => 0,
        ];

        $request = $this->container->get('request');

        foreach ($dates as $date) {
            foreach ($subscriptionPlans as $plan) {
                $code = $plan->getCode();
                $subscriptionName = $plan->getTranslation('name', $request->getLocale());

                $result['subscription_quantities'][$code]['quantities'][] = 0;
                $result['subscription_quantities'][$code]['name'] = $subscriptionName;
            }
        }

        foreach ($subscriptionReports as $subscriptionReport) {
            $date = $subscriptionReport->getDate()->format(AdminHelper::DATE_FORMAT);

            foreach ($subscriptionReport->getSubscriptionReportSubscriptions() as $subscriptionReportSubscription) {
                /** @var SubscriptionReportSubscription $subscriptionReportSubscription*/
                $code = $subscriptionReportSubscription->getSubscriptionPlan()->getCode();

                $subscriptionQuantity = $subscriptionReportSubscription->getQuantity();
                $subscriptionName = $subscriptionReportSubscription
                    ->getSubscriptionPlan()
                    ->getTranslation('name', $request->getLocale());

                $position = array_search($date, $result['dates']);

                if ($position !== false) {
                    $result['subscription_quantities'][$code]['quantities'][$position] += $subscriptionQuantity;
                    $result['subscription_quantities'][$code]['name'] = $subscriptionName;
                } else {
                    $subscriptionQuantity = 0;
                }

                if (isset($result['subscription_total_quantities'][$code])) {
                    $result['subscription_total_quantities'][$code]['quantity'] += $subscriptionQuantity;
                } else {
                    $result['subscription_total_quantities'][$code]['quantity'] = $subscriptionQuantity;
                    $result['subscription_total_quantities'][$code]['name'] = $subscriptionName;
                }

                $result['total_quantity'] += $subscriptionQuantity;
            }
        }

        ksort($result['subscription_total_quantities']);

        return $result;
    }

    public function saveSubscriptionStats()
    {
        /** @var SubscriptionPlanRepository $repo */
        $repo = $this->getSubscriptionPlanRepository();

        $stats = $repo->getSubscriptionStatistics();

        //Doctrine's LEFT JOIN dirty fix
        $counts = [];

        foreach ($stats as $stat) {
            $counts[$stat[0]->getId()] = $stat['cnt'];
        }

        foreach ($repo->findAll() as $subscriptionPlan) {
            if (!isset($counts[$subscriptionPlan->getId()])) {
                $stats[] = [
                    0 => $subscriptionPlan,
                    'cnt' => 0,
                ];
            }
        }

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
