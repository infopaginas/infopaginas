<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\Subscription;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Model\SubscriptionPlanInterface;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170612094249 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS subscription_report_subscription");
        $this->downgradeSuperVmSubscriptions();
        $this->addConfigValues();

        $businessProfileManager = $this->container->get('domain_business.manager.business_profile');

        $businessProfileManager->handleElasticSearchIndexRefresh();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function checkNewConfigValue($key)
    {
        $config = $this->em->getRepository(Config::class)->findOneBy(
            [
                'key' => $key,
            ]
        );

        return (bool)$config;
    }

    protected function downgradeSuperVmSubscriptions()
    {
        $premiumPlatinum = $this->em->getRepository(SubscriptionPlan::class)->findOneBy([
            'code' => SubscriptionPlanInterface::CODE_PREMIUM_PLATINUM,
        ]);

        if ($premiumPlatinum) {
            $superVm = $this->em->getRepository(SubscriptionPlan::class)->findOneBy([
                'code' => SubscriptionPlanInterface::CODE_SUPER_VM,
            ]);

            if ($superVm) {
                $subscriptions = $superVm->getSubscriptions();

                foreach ($subscriptions as $subscription) {
                    $subscription->setSubscriptionPlan($premiumPlatinum);
                }
            }
        }

        $this->em->flush();
    }

    protected function addConfigValues()
    {
        if (!$this->checkNewConfigValue(ConfigInterface::SEARCH_ADS_ALLOWED)) {
            $config = new Config();
            $config->setKey(ConfigInterface::SEARCH_ADS_ALLOWED);
            $config->setTitle('Allow ads for search results');
            $config->setValue(1);
            $config->setFormat('text');
            $config->setDescription('Set to 1 to enable ads for search results (0 - disable)');

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::SEARCH_ADS_MAX_PAGE)) {
            $config = new Config();
            $config->setKey(ConfigInterface::SEARCH_ADS_MAX_PAGE);
            $config->setTitle('Pages where ads will be shown');
            $config->setValue(1);
            $config->setFormat('text');
            $config->setDescription('Max page number where ads will be shown (set to 0 to show ads at all pages)');

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::SEARCH_ADS_PER_PAGE)) {
            $config = new Config();
            $config->setKey(ConfigInterface::SEARCH_ADS_PER_PAGE);
            $config->setTitle('Ads per page for search results');
            $config->setValue(6);
            $config->setFormat('text');
            $config->setDescription('Max ads quantity that can be shown at one search page (note it is max quantity)');

            $this->em->persist($config);
        }

        $this->em->flush();
    }
}
