<?php
namespace Domain\BusinessBundle\DataFixture\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Category;
use Oxa\Sonata\AdminBundle\Model\Fixture\OxaAbstractFixture;

class LoadTestCategoryReportData extends OxaAbstractFixture
{
    /**
     * @var int
     */
    protected $order = 5;

    protected function loadData()
    {
        $this->generateCategoryReports();
    }

    private function generateCategoryReports()
    {
        /** @var ObjectManager $manager */
        $manager = $this->manager;

        $categoryReportManager = $this->container->get('domain_report.manager.category_report_manager');

        $categories = $manager->getRepository('DomainBusinessBundle:Category')->findAll();

        // days ago number
        $daysQuantity = 10;

        $date = new \DateTime('today');
        $date->modify(sprintf('-%s days', $daysQuantity));

        // generate random report number for each subscription plan for each day
        // start from $daysQuantity days ago
        for ($i = 0; $i < $daysQuantity; $i++) {
            $date->modify('+1 day');
            foreach ($categories as $category) {
                foreach (range(0, rand(0, 3)) as $item) {
                    $categoryReportManager
                        ->registerBusinessVisit($category->getId(), $date);
                }
            }
        }

        $manager->flush();
    }
}
