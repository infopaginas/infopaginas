<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170718121034 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM article WHERE is_external = TRUE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {

    }
}
