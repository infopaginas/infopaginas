<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20190701105317 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewConfigValue(ConfigInterface::EXCEPTION_ERROR_TEMPLATE)) {
            $value = $this->container->get('twig')
                ->render('OxaConfigBundle:Fixtures:exception_error_template.html.twig');

            $configMail = new Config();
            $configMail->setKey(ConfigInterface::EXCEPTION_ERROR_TEMPLATE)
                ->setTitle('Found exception error Email Template')
                ->setValue($value)
                ->setFormat('html')
                ->setDescription('Found exception error')
                ->setIsActive(true);

            $this->em->persist($configMail);
        }

        $this->em->flush();

        $this->addSql('
            INSERT INTO config (key, title, value, format, description, created_at, updated_at, is_active)
            VALUES (
                \'EXCEPTION_ERROR_EMAIL_ADDRESS\',
                \'Exception error email address\',
                \'jizquierdo@infopaginas.com, tsantiago@infopaginas.com, mccpsp@infopaginas.com\',
                \'text\',
                \'All 4xx and 5xx errors will be sent to this email\',
                NOW(),
                NOW(),
                true
            ),
            (
                \'EXCEPTION_ERROR_EMAIL_SUBJECT\',
                \'Exception error subject\',
                \'Exception error\',
                \'text\',
                \'Subject of exception error email\',
                NOW(),
                NOW(),
                true
            )'
        );
    }

    public function down(Schema $schema)
    {
    }

    private function checkNewConfigValue($key)
    {
        return (bool)$this->em->getRepository(Config::class)->findOneBy(['key' => $key]);
    }
}
