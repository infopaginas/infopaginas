<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160518083936 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE area_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE business_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE brand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fos_user_group_translation (id SERIAL NOT NULL, object_id INT DEFAULT NULL, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7E45C860232D562B ON fos_user_group_translation (object_id)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_fos_user_group_translation_idx ON fos_user_group_translation (locale, object_id, field)');
        $this->addSql('CREATE TABLE area (id INT NOT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7943D68FDE969F2 ON area (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_D7943D68E104C1D3 ON area (created_user_id)');
        $this->addSql('CREATE INDEX IDX_D7943D68BB649746 ON area (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_D7943D6829A1466 ON area (is_active_user_id)');
        $this->addSql('CREATE TABLE subscription (id INT NOT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, code INT NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3C664D3FDE969F2 ON subscription (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3E104C1D3 ON subscription (created_user_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3BB649746 ON subscription (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D329A1466 ON subscription (is_active_user_id)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_389B783FDE969F2 ON tag (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_389B783E104C1D3 ON tag (created_user_id)');
        $this->addSql('CREATE INDEX IDX_389B783BB649746 ON tag (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_389B78329A1466 ON tag (is_active_user_id)');
        $this->addSql('CREATE TABLE business_profile (id INT NOT NULL, user_id INT NOT NULL, subscription_id INT DEFAULT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, website VARCHAR(30) NOT NULL, email VARCHAR(30) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, registration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, slogan VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, product TEXT DEFAULT NULL, working_hours VARCHAR(255) DEFAULT NULL, is_set_description BOOLEAN DEFAULT \'false\' NOT NULL, is_set_map BOOLEAN DEFAULT \'false\' NOT NULL, is_set_ad BOOLEAN DEFAULT \'false\' NOT NULL, is_set_logo BOOLEAN DEFAULT \'false\' NOT NULL, is_set_slogan BOOLEAN DEFAULT \'false\' NOT NULL, slug VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DC641142A76ED395 ON business_profile (user_id)');
        $this->addSql('CREATE INDEX IDX_DC6411429A1887DC ON business_profile (subscription_id)');
        $this->addSql('CREATE INDEX IDX_DC641142FDE969F2 ON business_profile (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_DC641142E104C1D3 ON business_profile (created_user_id)');
        $this->addSql('CREATE INDEX IDX_DC641142BB649746 ON business_profile (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_DC64114229A1466 ON business_profile (is_active_user_id)');
        $this->addSql('CREATE TABLE business_profile_categories (business_profile_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(business_profile_id, category_id))');
        $this->addSql('CREATE INDEX IDX_4FCD4F13C591A13 ON business_profile_categories (business_profile_id)');
        $this->addSql('CREATE INDEX IDX_4FCD4F1312469DE2 ON business_profile_categories (category_id)');
        $this->addSql('CREATE TABLE business_profile_areas (business_profile_id INT NOT NULL, area_id INT NOT NULL, PRIMARY KEY(business_profile_id, area_id))');
        $this->addSql('CREATE INDEX IDX_FD4FE795C591A13 ON business_profile_areas (business_profile_id)');
        $this->addSql('CREATE INDEX IDX_FD4FE795BD0F409C ON business_profile_areas (area_id)');
        $this->addSql('CREATE TABLE business_profile_tags (business_profile_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(business_profile_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_9A5C3601C591A13 ON business_profile_tags (business_profile_id)');
        $this->addSql('CREATE INDEX IDX_9A5C3601BAD26311 ON business_profile_tags (tag_id)');
        $this->addSql('CREATE TABLE business_profile_brands (business_profile_id INT NOT NULL, brand_id INT NOT NULL, PRIMARY KEY(business_profile_id, brand_id))');
        $this->addSql('CREATE INDEX IDX_9CBFC175C591A13 ON business_profile_brands (business_profile_id)');
        $this->addSql('CREATE INDEX IDX_9CBFC17544F5D008 ON business_profile_brands (brand_id)');
        $this->addSql('CREATE TABLE business_profile_payment_methods (business_profile_id INT NOT NULL, payment_method_id INT NOT NULL, PRIMARY KEY(business_profile_id, payment_method_id))');
        $this->addSql('CREATE INDEX IDX_C01C22FEC591A13 ON business_profile_payment_methods (business_profile_id)');
        $this->addSql('CREATE INDEX IDX_C01C22FE5AA1164F ON business_profile_payment_methods (payment_method_id)');
        $this->addSql('CREATE TABLE brand (id INT NOT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C52F958FDE969F2 ON brand (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_1C52F958E104C1D3 ON brand (created_user_id)');
        $this->addSql('CREATE INDEX IDX_1C52F958BB649746 ON brand (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_1C52F95829A1466 ON brand (is_active_user_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C1FDE969F2 ON category (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_64C19C1E104C1D3 ON category (created_user_id)');
        $this->addSql('CREATE INDEX IDX_64C19C1BB649746 ON category (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_64C19C129A1466 ON category (is_active_user_id)');
        $this->addSql('CREATE TABLE payment_method (id INT NOT NULL, deleted_user_id INT DEFAULT NULL, created_user_id INT DEFAULT NULL, updated_user_id INT DEFAULT NULL, is_active_user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B61A1F6FDE969F2 ON payment_method (deleted_user_id)');
        $this->addSql('CREATE INDEX IDX_7B61A1F6E104C1D3 ON payment_method (created_user_id)');
        $this->addSql('CREATE INDEX IDX_7B61A1F6BB649746 ON payment_method (updated_user_id)');
        $this->addSql('CREATE INDEX IDX_7B61A1F629A1466 ON payment_method (is_active_user_id)');
        $this->addSql('ALTER TABLE fos_user_group_translation ADD CONSTRAINT FK_7E45C860232D562B FOREIGN KEY (object_id) REFERENCES fos_user_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D6829A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D329A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B78329A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile ADD CONSTRAINT FK_DC641142A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile ADD CONSTRAINT FK_DC6411429A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile ADD CONSTRAINT FK_DC641142FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile ADD CONSTRAINT FK_DC641142E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile ADD CONSTRAINT FK_DC641142BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile ADD CONSTRAINT FK_DC64114229A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_categories ADD CONSTRAINT FK_4FCD4F13C591A13 FOREIGN KEY (business_profile_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_categories ADD CONSTRAINT FK_4FCD4F1312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_areas ADD CONSTRAINT FK_FD4FE795C591A13 FOREIGN KEY (business_profile_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_areas ADD CONSTRAINT FK_FD4FE795BD0F409C FOREIGN KEY (area_id) REFERENCES area (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_tags ADD CONSTRAINT FK_9A5C3601C591A13 FOREIGN KEY (business_profile_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_tags ADD CONSTRAINT FK_9A5C3601BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_brands ADD CONSTRAINT FK_9CBFC175C591A13 FOREIGN KEY (business_profile_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_brands ADD CONSTRAINT FK_9CBFC17544F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_payment_methods ADD CONSTRAINT FK_C01C22FEC591A13 FOREIGN KEY (business_profile_id) REFERENCES business_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE business_profile_payment_methods ADD CONSTRAINT FK_C01C22FE5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT FK_1C52F958FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT FK_1C52F958E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT FK_1C52F958BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT FK_1C52F95829A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C129A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_method ADD CONSTRAINT FK_7B61A1F6FDE969F2 FOREIGN KEY (deleted_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_method ADD CONSTRAINT FK_7B61A1F6E104C1D3 FOREIGN KEY (created_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_method ADD CONSTRAINT FK_7B61A1F6BB649746 FOREIGN KEY (updated_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_method ADD CONSTRAINT FK_7B61A1F629A1466 FOREIGN KEY (is_active_user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE business_profile_areas DROP CONSTRAINT FK_FD4FE795BD0F409C');
        $this->addSql('ALTER TABLE business_profile DROP CONSTRAINT FK_DC6411429A1887DC');
        $this->addSql('ALTER TABLE business_profile_tags DROP CONSTRAINT FK_9A5C3601BAD26311');
        $this->addSql('ALTER TABLE business_profile_categories DROP CONSTRAINT FK_4FCD4F13C591A13');
        $this->addSql('ALTER TABLE business_profile_areas DROP CONSTRAINT FK_FD4FE795C591A13');
        $this->addSql('ALTER TABLE business_profile_tags DROP CONSTRAINT FK_9A5C3601C591A13');
        $this->addSql('ALTER TABLE business_profile_brands DROP CONSTRAINT FK_9CBFC175C591A13');
        $this->addSql('ALTER TABLE business_profile_payment_methods DROP CONSTRAINT FK_C01C22FEC591A13');
        $this->addSql('ALTER TABLE business_profile_brands DROP CONSTRAINT FK_9CBFC17544F5D008');
        $this->addSql('ALTER TABLE business_profile_categories DROP CONSTRAINT FK_4FCD4F1312469DE2');
        $this->addSql('ALTER TABLE business_profile_payment_methods DROP CONSTRAINT FK_C01C22FE5AA1164F');
        $this->addSql('DROP SEQUENCE area_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subscription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE business_profile_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE brand_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_method_id_seq CASCADE');
        $this->addSql('DROP TABLE fos_user_group_translation');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE business_profile');
        $this->addSql('DROP TABLE business_profile_categories');
        $this->addSql('DROP TABLE business_profile_areas');
        $this->addSql('DROP TABLE business_profile_tags');
        $this->addSql('DROP TABLE business_profile_brands');
        $this->addSql('DROP TABLE business_profile_payment_methods');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE payment_method');
    }
}
