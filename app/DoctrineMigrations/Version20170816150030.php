<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Oxa\ConfigBundle\Model\ConfigInterface;

class Version20170816150030 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE config SET value = \'\' WHERE key = \'' . ConfigInterface::YOUTUBE_ACCESS_TOKEN . '\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }
}
