<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170809111331 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS page_template, page_template_translation CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
