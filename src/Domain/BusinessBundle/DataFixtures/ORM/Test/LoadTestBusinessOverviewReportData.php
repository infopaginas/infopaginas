<?php
namespace Domain\BusinessBundle\DataFixture\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Category;
use Oxa\Sonata\AdminBundle\Model\Fixture\OxaAbstractFixture;

class LoadTestBusinessOverviewReportData extends OxaAbstractFixture
{
    /**
     * @var int
     */
    protected $order = 9;

    protected function loadData()
    {
        $this->generateBusinessProfileReports();
    }

    private function generateBusinessProfileReports()
    {
        /** @var ObjectManager $manager */
        $manager = $this->manager;

        $businessOverviewReportManager = $this->container->get('domain_report.manager.business_overview_report_manager');

        $businessProfileIds = $manager->getRepository('DomainBusinessBundle:BusinessProfile')
            ->getIndexedBusinessProfileIds();

        // days ago number
        $daysQuantity = 60;

        $date = new \DateTime('today');
        $date->modify(sprintf('-%s days', $daysQuantity));

        // generate random report number for each subscription plan for each day
        // start from $daysQuantity days ago
        for ($i = 0; $i < $daysQuantity; $i++) {
            $businessOverviewReportDate = clone $date->modify('+1 day');

            for ($j = 0; $j < rand(0,40); $j++) {
                $businessProfileId = intval($businessProfileIds[array_rand($businessProfileIds)]);
                $businessOverviewReportManager->registerBusinessImpression($businessProfileId, $businessOverviewReportDate);
            }
            for ($j = 0; $j < rand(0,20); $j++) {
                $businessProfileId = intval($businessProfileIds[array_rand($businessProfileIds)]);
                $businessOverviewReportManager->registerBusinessImpression($businessProfileId, $businessOverviewReportDate);
                $businessOverviewReportManager->registerBusinessView($businessProfileId, $businessOverviewReportDate);
            }
        }
    }
}
