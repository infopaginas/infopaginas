<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20180912132952 extends AbstractMigration implements ContainerAwareInterface
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
    public function up(Schema $schema): void
    {
        $this->addConfigValues();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
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

    protected function addConfigValues()
    {
        if (!$this->checkNewConfigValue(ConfigInterface::IMPRESSIONS_FILTER_VALUE)) {
            $config = new Config();
            $config->setKey(ConfigInterface::IMPRESSIONS_FILTER_VALUE);
            $config->setTitle('Impressions filter boundary value ');
            $config->setValue(1000);
            $config->setFormat('text');
            $config->setDescription(
                'To change the filter by "less" or "more or equal" than this value'
            );
            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::DIRECTIONS_FILTER_VALUE)) {
            $config = new Config();
            $config->setKey(ConfigInterface::DIRECTIONS_FILTER_VALUE);
            $config->setTitle('Directions filter boundary value ');
            $config->setValue(1000);
            $config->setFormat('text');
            $config->setDescription(
                'To change the filter by "less" or "more or equal" than this value'
            );
            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::CALLS_MOBILE_FILTER_VALUE)) {
            $config = new Config();
            $config->setKey(ConfigInterface::CALLS_MOBILE_FILTER_VALUE);
            $config->setTitle('Calls mobile filter boundary value ');
            $config->setValue(1000);
            $config->setFormat('text');
            $config->setDescription(
                'To change the filter by "less" or "more or equal" than this value'
            );
            $this->em->persist($config);
        }

        $this->em->flush();
    }
}
