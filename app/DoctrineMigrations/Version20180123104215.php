<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20180123104215 extends AbstractMigration implements ContainerAwareInterface
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

    /**
     * @return bool
     */
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
        if (!$this->checkNewConfigValue(ConfigInterface::SUGGEST_CATEGORY_MINIMUM_MATCH)) {
            $config = new Config();
            $config->setKey(ConfigInterface::SUGGEST_CATEGORY_MINIMUM_MATCH);
            $config->setTitle('Minimum category match');
            $config->setValue(1);
            $config->setFormat('text');
            $config->setDescription('All categories are required when 0, if 1 than at least should 1 match');

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::SUGGEST_LOCALITY_MINIMUM_MATCH)) {
            $config = new Config();
            $config->setKey(ConfigInterface::SUGGEST_LOCALITY_MINIMUM_MATCH);
            $config->setTitle('Minimum locality match');
            $config->setValue(1);
            $config->setFormat('text');
            $config->setDescription('All localities are required when 0, if 1 than at least should 1 match');

            $this->em->persist($config);
        }

        $this->em->flush();
    }
}
