<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Version20180718115032
 *
 * @package Application\Migrations
 */
class Version20180718115032 extends AbstractMigration implements ContainerAwareInterface
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
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em        = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$this->checkNewConfigValue(ConfigInterface::MAIL_SUGGEST_EDITS_PROCESSED_TEMPLATE)) {
            $value = $this->container->get('twig')
                ->render('OxaConfigBundle:Fixtures:mail_suggest_edits_processed.html.twig');

            $configMail = new Config();
            $configMail->setKey(ConfigInterface::MAIL_SUGGEST_EDITS_PROCESSED_TEMPLATE);
            $configMail->setTitle('Suggest edits were processed');
            $configMail->setValue($value);
            $configMail->setFormat('html');
            $configMail->setDescription('Notify if suggest edits were processed');

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

    /**
     * @param int $key
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
