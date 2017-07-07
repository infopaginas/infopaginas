<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Entity\Config;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170706123258 extends AbstractMigration implements ContainerAwareInterface
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
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addNotificationTemplate();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }

    protected function addNotificationTemplate()
    {
        $configMail = $this->getConfigByKey(ConfigInterface::MAIL_REPORT_EXPORT_PROCESSED);

        $value = $this->container->get('twig')
            ->render('OxaConfigBundle:Fixtures:mail_export_report_processed.html.twig');

        $configMail->setKey(ConfigInterface::MAIL_REPORT_EXPORT_PROCESSED);
        $configMail->setTitle('Export report processed template');
        $configMail->setValue($value);
        $configMail->setFormat('html');
        $configMail->setDescription('Notify if export report is processed');

        $this->em->persist($configMail);

        $this->em->flush();
    }

    /**
     * @param string $key
     *
     * @return Config
     */
    protected function getConfigByKey($key)
    {
        $config = $this->em->getRepository(Config::class)->findOneBy(
            [
                'key' => $key,
            ]
        );

        if (!$config) {
            $config = new Config();
        }

        return $config;
    }
}
