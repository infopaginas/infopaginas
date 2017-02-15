<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214094528 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewConfigValue(ConfigInterface::YOUTUBE_ACCESS_TOKEN)) {
            $config = new Config();
            $config->setKey(ConfigInterface::YOUTUBE_ACCESS_TOKEN);
            $config->setTitle('Youtube access token');
            $config->setValue(json_encode([]));
            $config->setFormat('json');
            $config->setDescription('Token to access youtube account');
            $config->setIsActive(false);

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::YOUTUBE_ERROR_EMAIL_TEMPLATE)) {
            $value = $this->container->get('twig')
                ->render('OxaConfigBundle:Fixtures:mail_youtube_token_invalid.html.twig');


            $configMail = new Config();
            $configMail->setKey(ConfigInterface::YOUTUBE_ERROR_EMAIL_TEMPLATE);
            $configMail->setTitle('Youtube token error template');
            $configMail->setValue($value);
            $configMail->setFormat('html');
            $configMail->setDescription('Notify if youtube token is invalid');

            $this->em->persist($configMail);
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
