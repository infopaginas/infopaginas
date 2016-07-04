<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160529144512 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE business_profile ADD closure_reason VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD review_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task DROP discr');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB253E2E969B FOREIGN KEY (review_id) REFERENCES business_review (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB253E2E969B ON task (review_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB253E2E969B');
        $this->addSql('DROP INDEX UNIQ_527EDB253E2E969B');
        $this->addSql('ALTER TABLE task ADD discr VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE task DROP review_id');
        $this->addSql('ALTER TABLE business_profile DROP closure_reason');
    }
}
