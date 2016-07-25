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

class LoadTestSubscriptionReportData extends OxaAbstractFixture
{
    /**
     * @var int
     */
    protected $order = 5;

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
