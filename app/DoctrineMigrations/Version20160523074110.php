<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160523074110 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE business_review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE business_review (id INT NOT NULL, user_id INT DEFAULT NULL, business_review_id INT DEFAULT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, username VARCHAR(100) DEFAULT NULL, rate INT DEFAULT NULL, content TEXT NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22E3CE3CA76ED395 ON business_review (user_id)');
        $this->addSql('CREATE INDEX IDX_22E3CE3C274A90E4 ON business_review (business_review_id)');
        $this->addSql('CREATE INDEX IDX_22E3CE3CFDE969F2 ON business_review (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_22E3CE3CE104C1D3 ON business_review (created_user_id)');
        $this->addSql('CREATE INDEX IDX_22E3CE3CBB649746 ON business_review (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_22E3CE3C29A1466 ON business_review (is_active_user_id)');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3C274A90E4 FOREIGN KEY (business_review_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3CFDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3CE104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3CBB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_review ADD CONSTRAINT FK_22E3CE3C29A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fos_user_group ALTER is_active SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE fos_user_user ALTER biography TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE fos_user_user ALTER is_active SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE business_profile ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT fk_527edb255a03bd95');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB2570574616');
        $this->addSql('DROP INDEX uniq_527edb255a03bd95');
        $this->addSql('ALTER TABLE task ADD deleted_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD created_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD updated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD is_active_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD is_active BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE task ADD discr VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE task RENAME COLUMN business_profile_od TO business_review_id');
        $this->addSql('ALTER TABLE task RENAME COLUMN modified_at TO updated_at');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25274A90E4 FOREIGN KEY (business_review_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2529A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2570574616 FOREIGN KEY (reviewer_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB25274A90E4 ON task (business_review_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25FDE969F2 ON task (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25E104C1D3 ON task (created_user_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25BB649746 ON task (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_527EDB2529A1466 ON task (is_active_user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE business_review_id_seq CASCADE');
        $this->addSql('DROP TABLE business_review');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25274A90E4');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25FDE969F2');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25E104C1D3');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25BB649746');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB2529A1466');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT fk_527edb2570574616');
        $this->addSql('DROP INDEX IDX_527EDB25274A90E4');
        $this->addSql('DROP INDEX IDX_527EDB25FDE969F2');
        $this->addSql('DROP INDEX IDX_527EDB25E104C1D3');
        $this->addSql('DROP INDEX IDX_527EDB25BB649746');
        $this->addSql('DROP INDEX IDX_527EDB2529A1466');
        $this->addSql('ALTER TABLE task ADD business_profile_od INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task DROP business_review_id');
        $this->addSql('ALTER TABLE task DROP deleted_user_id');
        $this->addSql('ALTER TABLE task DROP created_user_id');
        $this->addSql('ALTER TABLE task DROP updated_user_id');
        $this->addSql('ALTER TABLE task DROP is_active_user_id');
        $this->addSql('ALTER TABLE task DROP deleted_at');
        $this->addSql('ALTER TABLE task DROP is_active');
        $this->addSql('ALTER TABLE task DROP discr');
        $this->addSql('ALTER TABLE task RENAME COLUMN updated_at TO modified_at');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT fk_527edb255a03bd95 FOREIGN KEY (business_profile_od) REFERENCES business_profile (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT fk_527edb2570574616 FOREIGN KEY (reviewer_id) REFERENCES fos_user_user (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_527edb255a03bd95 ON task (business_profile_od)');
        $this->addSql('ALTER TABLE fos_user_group ALTER is_active DROP DEFAULT');
        $this->addSql('ALTER TABLE business_profile ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE fos_user_user ALTER biography TYPE VARCHAR(1000)');
        $this->addSql('ALTER TABLE fos_user_user ALTER is_active DROP DEFAULT');
    }
}
