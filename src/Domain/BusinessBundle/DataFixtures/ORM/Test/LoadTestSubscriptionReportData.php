<?php
namespace Domain\BusinessBundle\DataFixture\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\SubscriptionReportSubscription;
use Oxa\Sonata\AdminBundle\Model\Fixture\OxaAbstractFixture;

class LoadTestSubscriptionReportData extends OxaAbstractFixture
{
    /**
     * @var int
     */
    protected $order = 10;

    protected function loadData()
    {
        $this->generateSubscriptionReports();
    }

    private function generateSubscriptionReports()
    {
        /** @var ObjectManager $manager */
        $manager = $this->manager;

        $subscriptionPlans = $manager->getRepository('DomainBusinessBundle:SubscriptionPlan')->findAll();

        // days ago number
        $daysQuantity = 60;

        $date = new \DateTime('today');
        $date->modify(sprintf('-%s days', $daysQuantity));

        // generate random report number for each subscription plan for each day
        // start from $daysQuantity days ago
        for ($i = 0; $i < $daysQuantity; $i++) {
            $subscriptionDate = clone $date->modify('+1 day');
            $subscriptionReport = new SubscriptionReport();
            $subscriptionReport->setDate($subscriptionDate);

            foreach ($subscriptionPlans as $subscriptionPlan) {
                $subscriptionReportSubscription = new SubscriptionReportSubscription();
                $subscriptionReportSubscription->setSubscriptionPlan($subscriptionPlan);
                $subscriptionReportSubscription->setQuantity(rand(1, 50));
                $subscriptionReportSubscription->setSubscriptionReport($subscriptionReport);
                $manager->persist($subscriptionReportSubscription);
            }

            $manager->persist($subscriptionReport);
        }
    }
}
