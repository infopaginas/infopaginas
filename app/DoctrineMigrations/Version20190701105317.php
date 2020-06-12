<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
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

    public function up(Schema $schema): void
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

        if (!$this->checkNewConfigValue(ConfigInterface::EXCEPTION_ERROR_EMAIL_ADDRESS)) {
            $configEmailAddress = new Config();
            $configEmailAddress->setKey(ConfigInterface::EXCEPTION_ERROR_EMAIL_ADDRESS)
                ->setTitle('Exception error email addresses')
                ->setValue('jizquierdo@infopaginas.com, tsantiago@infopaginas.com, mccpsp@infopaginas.com')
                ->setFormat('text')
                ->setDescription('All 4xx and 5xx errors will be sent to this emails')
                ->setIsActive(true);

            $this->em->persist($configEmailAddress);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::EXCEPTION_ERROR_EMAIL_SUBJECT)) {
            $configEmailSubject = new Config();
            $configEmailSubject->setKey(ConfigInterface::EXCEPTION_ERROR_EMAIL_SUBJECT)
                ->setTitle('Exception error subject')
                ->setValue('Exception error')
                ->setFormat('text')
                ->setDescription('Subject of exception error email')
                ->setIsActive(true);

            $this->em->persist($configEmailSubject);
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
