<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170911123343 extends AbstractMigration implements ContainerAwareInterface
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
        if (!$this->checkNewConfigValue(ConfigInterface::FEEDBACK_EMAIL_ADDRESS)) {
            $config = new Config();
            $config->setKey(ConfigInterface::FEEDBACK_EMAIL_ADDRESS);
            $config->setTitle('Feedback email address');
            $config->setValue('contacto@infopaginas.com');
            $config->setFormat('text');
            $config->setDescription('Users\' feedbacks will be sent to this email');
            $config->setIsActive(true);

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::FEEDBACK_EMAIL_SUBJECT)) {
            $config = new Config();
            $config->setKey(ConfigInterface::FEEDBACK_EMAIL_SUBJECT);
            $config->setTitle('Feedback subject');
            $config->setValue('User\'s feedback from Infopaginas');
            $config->setFormat('text');
            $config->setDescription('Subject of email feedback');
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

    /**
     * @param int $key
     *
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
}
