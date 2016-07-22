<?php
namespace Domain\BusinessBundle\DataFixture\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Domain\BusinessBundle\Entity\Area;
use Domain\BusinessBundle\Entity\Brand;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Entity\Tag;
use Domain\BusinessBundle\Entity\Translation\AreaTranslation;
use Domain\BusinessBundle\Entity\Translation\BrandTranslation;
use Domain\BusinessBundle\Entity\Translation\BusinessProfileTranslation;
use Domain\BusinessBundle\Entity\Translation\CategoryTranslation;
use Domain\BusinessBundle\Entity\Translation\PaymentMethodTranslation;
use Domain\BusinessBundle\Entity\Translation\TagTranslation;
use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Entity\CategoryReportCategory;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\SubscriptionReportSubscription;
use Oxa\Sonata\AdminBundle\Model\Fixture\OxaAbstractFixture;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Entity\User;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\Date;

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
