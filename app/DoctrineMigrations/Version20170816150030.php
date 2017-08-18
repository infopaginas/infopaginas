<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170816150030 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var $container ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $key = $this->container->getParameter('google_map_api_key');

        $this->addSql('UPDATE config SET value = \'\' WHERE key = \'' . ConfigInterface::YOUTUBE_ACCESS_TOKEN . '\'');
        $this->addSql(
            'UPDATE config SET value = \'' . $key . '\' WHERE key = \'' . ConfigInterface::GOOGLE_API_KEY . '\''
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
