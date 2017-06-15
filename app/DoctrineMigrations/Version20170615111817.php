<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170615111817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE video_media SET status = \'active\' WHERE status IS NULL OR status = \'\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
