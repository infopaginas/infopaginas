<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170714120212 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS campaign, campaign_areas, campaign_translation CASCADE');
        $this->addSql('DROP TABLE IF EXISTS category_report_category');
        $this->addSql('DROP TABLE IF EXISTS double_click_company, double_click_line_item, double_click_order,
            double_click_synch_log CASCADE');
        $this->addSql('DROP TABLE IF EXISTS keywords');
        $this->addSql('DROP TABLE IF EXISTS interaction');
        $this->addSql('DROP TABLE IF EXISTS menu');
        $this->addSql('DROP TABLE IF EXISTS wistia_media_embeds, wistia_media_thumbnails, wistia_medias CASCADE');

        $this->addSql('DROP SEQUENCE IF EXISTS double_click_company_id_seq, double_click_line_item_id_seq,
            double_click_order_id_seq, double_click_synch_log_id_seq CASCADE');

        $this->addSql('DROP SEQUENCE IF EXISTS interaction_id_seq, keywords_id_seq, menu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE IF EXISTS search_logs_id_seq CASCADE');

        $this->addSql('DELETE FROM category_report');
        $this->addSql('DELETE FROM subscription_report');
        $this->addSql('DELETE FROM user_action_report');
        $this->addSql('DELETE FROM visitor');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
