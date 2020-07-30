<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200218170802 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewConfigValue(ConfigInterface::OFFICE_COORDINATES)) {
            $config = new Config();
            $config->setKey(ConfigInterface::OFFICE_COORDINATES);
            $config->setTitle('Office coordinates');
            $config->setValue('18.414479,-66.104365');
            $config->setFormat('text');
            $config->setDescription('Office coordinates');
            $config->setIsActive(true);

            $this->em->persist($config);
            $this->em->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
    }

    protected function checkNewConfigValue($key)
    {
        $config = $this->em->getRepository('OxaConfigBundle:Config')->findOneBy(
            [
                'key' => $key,
            ]
        );

        return (bool)$config;
    }
}
