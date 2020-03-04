<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20200303171738 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewConfigValue(ConfigInterface::DELETE_PROFILE_ALERT_EMAIL_ADDRESS)) {
            $config = new Config();
            $config->setKey(ConfigInterface::DELETE_PROFILE_ALERT_EMAIL_ADDRESS);
            $config->setTitle('Delete Profile Alert Email Address');
            $config->setValue('jizquierdo@infopaginas.com,tsantiago@infopaginas.com');
            $config->setFormat('text');
            $config->setDescription('Delete Profile Alert will be send to this email');
            $config->setIsActive(true);

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::DELETE_PROFILE_ALERT_EMAIL_SUBJECT)) {
            $config = new Config();
            $config->setKey(ConfigInterface::DELETE_PROFILE_ALERT_EMAIL_SUBJECT);
            $config->setTitle('Delete Profile Alert Email Subject');
            $config->setValue('Business Profile Deletion');
            $config->setFormat('text');
            $config->setDescription('Subject of delete profile alert email');
            $config->setIsActive(true);

            $this->em->persist($config);
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
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
