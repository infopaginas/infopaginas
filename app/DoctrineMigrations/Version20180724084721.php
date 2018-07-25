<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Version20180724084721
 *
 * @package Application\Migrations
 */
class Version20180724084721 extends AbstractMigration implements ContainerAwareInterface
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
     * @param ContainerInterface|null $container
     */
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
        if (!$this->checkNewConfigValue(ConfigInterface::UPDATE_PROFILE_REQUEST_EMAIL_TEMPLATE)) {
            $value = $this->container->get('twig')
                ->render('OxaConfigBundle:Fixtures:mail_update_profile_request.html.twig');

            $configMail = new Config();
            $configMail->setKey(ConfigInterface::UPDATE_PROFILE_REQUEST_EMAIL_TEMPLATE)
                ->setTitle('Update Profile Request Email Template')
                ->setValue($value)
                ->setFormat('html')
                ->setDescription('Update Profile Request')
                ->setIsActive(true);

            $this->em->persist($configMail);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::UPDATE_PROFILE_REQUEST_EMAIL_ADDRESS)) {
            $config = new Config();
            $config->setKey(ConfigInterface::UPDATE_PROFILE_REQUEST_EMAIL_ADDRESS)
                ->setTitle('Update Profile Request Email Address')
                ->setValue('contacto@infopaginas.com')
                ->setFormat('text')
                ->setDescription('Update Profile Requests will be sent to this email')
                ->setIsActive(true);

            $this->em->persist($config);
        }

        if (!$this->checkNewConfigValue(ConfigInterface::UPDATE_PROFILE_REQUEST_EMAIL_SUBJECT)) {
            $config = new Config();
            $config->setKey(ConfigInterface::UPDATE_PROFILE_REQUEST_EMAIL_SUBJECT)
                ->setTitle('Update Profile Request subject')
                ->setValue('Update Profile Request')
                ->setFormat('text')
                ->setDescription('Subject of email Update Profile Request')
                ->setIsActive(true);

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

    /**
     * @param $key
     *
     * @return bool
     */
    private function checkNewConfigValue($key)
    {
        $config = $this->em->getRepository(Config::class)->findOneBy(
            [
                'key' => $key,
            ]
        );

        return (bool)$config;
    }
}
