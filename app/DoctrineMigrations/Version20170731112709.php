<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170731112709 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS banner_template, banner_template_translation CASCADE');
        $this->addSql('DROP TABLE IF EXISTS banner_type, banner_type_translation CASCADE');
        $this->addSql('DROP TABLE IF EXISTS banner_translation CASCADE');

        $this->addSql('DELETE FROM banner');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }
}
