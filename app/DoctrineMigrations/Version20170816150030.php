<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Version20170816150030 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE config SET value = \'\' WHERE key = \'' . ConfigInterface::YOUTUBE_ACCESS_TOKEN . '\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
