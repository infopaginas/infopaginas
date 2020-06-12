<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\EmergencyBundle\Entity\EmergencyCategory;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20171013105459 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $em \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em        = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->createConfigValue();
        $this->updateEmergencyCategorySearchName();

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }

    protected function createConfigValue()
    {
        $config = $this->em->getRepository(Config::class)->findOneBy([
            'key' => ConfigInterface::EMERGENCY_CATALOG_ORDER_BY_ALPHABET,
        ]);

        if (!$config) {
            $config = new Config();
            $config->setKey(ConfigInterface::EMERGENCY_CATALOG_ORDER_BY_ALPHABET);
            $config->setTitle('Emergency catalog is sorted by alphabet');
            $config->setValue('1');
            $config->setFormat('text');
            $config->setDescription(
                'If enabled category in emergency catalog will be sorted by alphabet otherwise - by category position'
            );
            $config->setIsActive(true);

            $this->em->persist($config);
        }
    }

    protected function updateEmergencyCategorySearchName()
    {
        $categories = $this->em->getRepository(EmergencyCategory::class)->findAll();

        foreach ($categories as $category) {
            $category->updateSearchName();
        }
    }
}
