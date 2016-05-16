<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160516074028 extends AbstractMigration
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

        $this->addSql('CREATE SEQUENCE fos_user_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE fos_user_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media__gallery_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media__gallery_media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media__media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('
          CREATE TABLE fos_user_group (
          id INT NOT NULL,
          deleted_user_id INT DEFAULT NULL,
          created_user_id INT DEFAULT NULL,
          updated_user_id INT DEFAULT NULL,
          is_active_user_id INT DEFAULT NULL,
          name VARCHAR(255) NOT NULL,
          roles TEXT NOT NULL,
          code INT DEFAULT 1 NOT NULL,
          description TEXT DEFAULT NULL,
          deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          is_active BOOLEAN NOT NULL,
          PRIMARY KEY(id))
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_583D1F3E5E237E06 ON fos_user_group (name)');

        $this->addSql('CREATE INDEX IDX_583D1F3EFDE969F2 ON fos_user_group (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_583D1F3EE104C1D3 ON fos_user_group (created_user_id)');
        $this->addSql('CREATE INDEX IDX_583D1F3EBB649746 ON fos_user_group (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_583D1F3E29A1466 ON fos_user_group (is_active_user_id)');

        $this->addSql('COMMENT ON COLUMN fos_user_group.roles IS \'(DC2Type:array)\'');

        $this->addSql('
          CREATE TABLE fos_user_user (
          id INT NOT NULL,
          group_id INT NOT NULL,
          is_active_user_id INT DEFAULT NULL,
          deleted_user_id INT DEFAULT NULL,
          created_user_id INT DEFAULT NULL,
          updated_user_id INT DEFAULT NULL,
          username VARCHAR(255) NOT NULL,
          username_canonical VARCHAR(255) NOT NULL,
          email VARCHAR(255) NOT NULL,
          email_canonical VARCHAR(255) NOT NULL,
          enabled BOOLEAN NOT NULL,
          salt VARCHAR(255) NOT NULL,
          password VARCHAR(255) NOT NULL,
          last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          locked BOOLEAN NOT NULL,
          expired BOOLEAN NOT NULL,
          expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          confirmation_token VARCHAR(255) DEFAULT NULL,
          password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          roles TEXT NOT NULL,
          credentials_expired BOOLEAN NOT NULL,
          credentials_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          date_of_birth TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          firstname VARCHAR(64) DEFAULT NULL,
          lastname VARCHAR(64) DEFAULT NULL,
          website VARCHAR(64) DEFAULT NULL,
          biography VARCHAR(1000) DEFAULT NULL,
          gender VARCHAR(1) DEFAULT NULL,
          locale VARCHAR(8) DEFAULT NULL,
          timezone VARCHAR(64) DEFAULT NULL,
          phone VARCHAR(64) DEFAULT NULL,
          facebook_uid VARCHAR(255) DEFAULT NULL,
          facebook_name VARCHAR(255) DEFAULT NULL,
          facebook_data TEXT DEFAULT NULL,
          twitter_uid VARCHAR(255) DEFAULT NULL,
          twitter_name VARCHAR(255) DEFAULT NULL,
          twitter_data TEXT DEFAULT NULL,
          gplus_uid VARCHAR(255) DEFAULT NULL,
          gplus_name VARCHAR(255) DEFAULT NULL,
          gplus_data TEXT DEFAULT NULL,
          token VARCHAR(255) DEFAULT NULL,
          two_step_code VARCHAR(255) DEFAULT NULL,
          is_active BOOLEAN NOT NULL,
          deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          PRIMARY KEY(id))
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_C560D76192FC23A8 ON fos_user_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C560D761A0D96FBF ON fos_user_user (email_canonical)');

        $this->addSql('CREATE INDEX IDX_C560D761FE54D947 ON fos_user_user (group_id)');
        $this->addSql('CREATE INDEX IDX_C560D76129A1466 ON fos_user_user (is_active_user_id)');
        $this->addSql('CREATE INDEX IDX_C560D761FDE969F2 ON fos_user_user (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_C560D761E104C1D3 ON fos_user_user (created_user_id)');
        $this->addSql('CREATE INDEX IDX_C560D761BB649746 ON fos_user_user (updated_user_id)');

        $this->addSql('COMMENT ON COLUMN fos_user_user.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN fos_user_user.facebook_data IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN fos_user_user.twitter_data IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN fos_user_user.gplus_data IS \'(DC2Type:json)\'');

        $this->addSql('
          CREATE TABLE fos_user_user_group (
            user_id INT NOT NULL,
            group_id INT NOT NULL,
            PRIMARY KEY(user_id, group_id)
          )
        ');

        $this->addSql('CREATE INDEX IDX_B3C77447A76ED395 ON fos_user_user_group (user_id)');
        $this->addSql('CREATE INDEX IDX_B3C77447FE54D947 ON fos_user_user_group (group_id)');
        
        $this->addSql('
          CREATE TABLE media__gallery (
            id INT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            context VARCHAR(64) NOT NULL, 
            default_format VARCHAR(255) NOT NULL, 
            enabled BOOLEAN NOT NULL, 
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            PRIMARY KEY(id)
          )
        ');
        
        $this->addSql('
          CREATE TABLE media__gallery_media (
            id INT NOT NULL, 
            gallery_id INT DEFAULT NULL, 
            media_id INT DEFAULT NULL, 
            position INT NOT NULL, 
            enabled BOOLEAN NOT NULL, 
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            PRIMARY KEY(id)
          )
        ');
        
        $this->addSql('CREATE INDEX IDX_80D4C5414E7AF8F ON media__gallery_media (gallery_id)');
        $this->addSql('CREATE INDEX IDX_80D4C541EA9FDD75 ON media__gallery_media (media_id)');
        
        $this->addSql('
          CREATE TABLE media__media (
            id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            enabled BOOLEAN NOT NULL,
            provider_name VARCHAR(255) NOT NULL,
            provider_status INT NOT NULL,
            provider_reference VARCHAR(255) NOT NULL,
            provider_metadata TEXT DEFAULT NULL,
            width INT DEFAULT NULL,
            height INT DEFAULT NULL,
            length NUMERIC(10, 0) DEFAULT NULL,
            content_type VARCHAR(255) DEFAULT NULL,
            content_size INT DEFAULT NULL,
            copyright VARCHAR(255) DEFAULT NULL,
            author_name VARCHAR(255) DEFAULT NULL,
            context VARCHAR(64) DEFAULT NULL,
            cdn_is_flushable BOOLEAN DEFAULT NULL,
            cdn_flush_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            cdn_status INT DEFAULT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
          )
        ');
        
        $this->addSql('COMMENT ON COLUMN media__media.provider_metadata IS \'(DC2Type:json)\'');
        
        $this->addSql('
          ALTER TABLE fos_user_group 
          ADD CONSTRAINT FK_583D1F3EFDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) 
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        
        $this->addSql('
          ALTER TABLE fos_user_group 
          ADD CONSTRAINT FK_583D1F3EE104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) 
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        
        $this->addSql('
          ALTER TABLE fos_user_group
          ADD CONSTRAINT FK_583D1F3EBB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_group
          ADD CONSTRAINT FK_583D1F3E29A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user
          ADD CONSTRAINT FK_C560D761FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user_group (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user
          ADD CONSTRAINT FK_C560D76129A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user
          ADD CONSTRAINT FK_C560D761FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user
          ADD CONSTRAINT FK_C560D761E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user
          ADD CONSTRAINT FK_C560D761BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user_group
          ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) ON DELETE CASCADE
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE fos_user_user_group
          ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user_group (id) ON DELETE CASCADE
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE media__gallery_media
          ADD CONSTRAINT FK_80D4C5414E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id)
          NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
          ALTER TABLE media__gallery_media
          ADD CONSTRAINT FK_80D4C541EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id)
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
        $this->addSql('ALTER TABLE fos_user_user DROP CONSTRAINT FK_C560D761FE54D947');
        $this->addSql('ALTER TABLE fos_user_user_group DROP CONSTRAINT FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE fos_user_group DROP CONSTRAINT FK_583D1F3EFDE969F2');
        $this->addSql('ALTER TABLE fos_user_group DROP CONSTRAINT FK_583D1F3EE104C1D3');
        $this->addSql('ALTER TABLE fos_user_group DROP CONSTRAINT FK_583D1F3EBB649746');
        $this->addSql('ALTER TABLE fos_user_group DROP CONSTRAINT FK_583D1F3E29A1466');
        $this->addSql('ALTER TABLE fos_user_user DROP CONSTRAINT FK_C560D76129A1466');
        $this->addSql('ALTER TABLE fos_user_user DROP CONSTRAINT FK_C560D761FDE969F2');
        $this->addSql('ALTER TABLE fos_user_user DROP CONSTRAINT FK_C560D761E104C1D3');
        $this->addSql('ALTER TABLE fos_user_user DROP CONSTRAINT FK_C560D761BB649746');
        $this->addSql('ALTER TABLE fos_user_user_group DROP CONSTRAINT FK_B3C77447A76ED395');
        $this->addSql('ALTER TABLE media__gallery_media DROP CONSTRAINT FK_80D4C5414E7AF8F');
        $this->addSql('ALTER TABLE media__gallery_media DROP CONSTRAINT FK_80D4C541EA9FDD75');
        $this->addSql('DROP SEQUENCE fos_user_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE fos_user_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media__gallery_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media__gallery_media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media__media_id_seq CASCADE');
        $this->addSql('DROP TABLE fos_user_group');
        $this->addSql('DROP TABLE fos_user_user');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE media__gallery');
        $this->addSql('DROP TABLE media__gallery_media');
        $this->addSql('DROP TABLE media__media');
    }
}
