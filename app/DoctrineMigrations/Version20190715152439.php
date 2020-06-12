<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20190715152439 extends AbstractMigration implements ContainerAwareInterface
{
    private $em;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function up(Schema $schema): void
    {
        if (!$this->checkNewConfigValue(ConfigInterface::HOMEPAGE_CAROUSEL_MAX_ELEMENT_COUNT)) {
            $configMaxElementCount = new Config();
            $configMaxElementCount->setKey(ConfigInterface::HOMEPAGE_CAROUSEL_MAX_ELEMENT_COUNT)
                ->setTitle('Homepage carousel maximum element count')
                ->setValue(10)
                ->setFormat('text')
                ->setDescription('Homepage carousel maximum element count')
                ->setIsActive(true);

            $this->em->persist($configMaxElementCount);
        }

        $this->em->flush();
    }

    public function down(Schema $schema): void
    {
    }

    private function checkNewConfigValue($key)
    {
        return (bool)$this->em->getRepository(Config::class)->findOneBy(['key' => $key]);
    }
}
