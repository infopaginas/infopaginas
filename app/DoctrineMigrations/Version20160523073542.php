<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160523073542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('
          CREATE TABLE task (
            id INT NOT NULL,
            business_profile_od INT DEFAULT NULL,
            reviewer_id INT DEFAULT NULL,
            type VARCHAR(255) CHECK(type IN (
              \'PROFILE_CREATE\', \'PROFILE_UPDATE\', \'PROFILE_CLOSE\', \'REVIEW_APPROVE\'
            )) NOT NULL,
            status VARCHAR(255) CHECK(status IN (\'OPEN\', \'CLOSED\', \'REJECTED\')) NOT NULL,
            reject_reason TEXT DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            modified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
          )
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB255A03BD95 ON task (business_profile_od)');

        $this->addSql('CREATE INDEX IDX_527EDB2570574616 ON task (reviewer_id)');

        $this->addSql('COMMENT ON COLUMN task.type IS \'(DC2Type:TaskType)\'');
        $this->addSql('COMMENT ON COLUMN task.status IS \'(DC2Type:TaskStatusType)\'');

        $this->addSql('
          ALTER TABLE task
          ADD CONSTRAINT FK_527EDB255A03BD95 FOREIGN KEY (business_profile_od)
          REFERENCES business_profile (id) ON DELETE SET NULL
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');

        $this->addSql('
          ALTER TABLE task
          ADD CONSTRAINT FK_527EDB2570574616 FOREIGN KEY (reviewer_id)
          REFERENCES fos_user_user (id) ON DELETE SET NULL
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB255A03BD95');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP TABLE task');
    }
}
