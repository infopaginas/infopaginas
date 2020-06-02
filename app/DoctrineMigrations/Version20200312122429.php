<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20200312122429 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewConfigValue(ConfigInterface::BUSINESS_PROFILE_POPUP_TIME_TO_APPEAR)) {
            $config = new Config();
            $config->setKey(ConfigInterface::BUSINESS_PROFILE_POPUP_TIME_TO_APPEAR);
            $config->setTitle('Business profile popup time to appear');
            $config->setValue('3');
            $config->setFormat('text');
            $config->setDescription('Business profile popup time to appear in seconds');
            $config->setIsActive(true);

            $this->em->persist($config);
        }

        $this->em->flush();
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
