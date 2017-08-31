<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170830115857 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE business_profile SET name = name_es WHERE name_es IS NOT NULL OR name_es != \'\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
