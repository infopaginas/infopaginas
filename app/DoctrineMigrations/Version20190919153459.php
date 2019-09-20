<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20190919153459 extends AbstractMigration implements ContainerAwareInterface
{
    private $em;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function up(Schema $schema)
    {
        if (!$this->checkNewConfigValue(ConfigInterface::FOOTER_EMAIL)) {
            $configFooterEmail = new Config();
            $configFooterEmail->setKey(ConfigInterface::FOOTER_EMAIL)
                ->setTitle('Footer email address')
                ->setValue('servicioalcliente@infopaginas.com')
                ->setFormat('text')
                ->setDescription('Email address displaying in footer')
                ->setIsActive(true);

            $this->em->persist($configFooterEmail);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::FOOTER_PHONE_NUMBER)) {
            $configFooterPhoneNumber = new Config();
            $configFooterPhoneNumber->setKey(ConfigInterface::FOOTER_PHONE_NUMBER)
                ->setTitle('Footer phone number')
                ->setValue('(787) 625-0555')
                ->setFormat('text')
                ->setDescription('Phone number displaying in footer')
                ->setIsActive(true);

            $this->em->persist($configFooterPhoneNumber);
        }

        $configFooterContent = $this->em->getRepository(Config::class)->findOneBy(['key' => 'FOOTER_CONTENT']);
        if ($configFooterContent) {
            $this->em->remove($configFooterContent);
        }

        $this->em->flush();
    }

    public function down(Schema $schema)
    {
    }

    private function checkNewConfigValue($key)
    {
        return (bool)$this->em->getRepository(Config::class)->findOneBy(['key' => $key]);
    }
}
