<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;

class Version20170814145754 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'UPDATE business_profile_phone
            SET
                type = \'' . BusinessProfilePhone::PHONE_TYPE_MAIN . '\',
                priority = ' . BusinessProfilePhone::PHONE_PRIORITY_MAIN . '
            WHERE id IN (
                SELECT p.id FROM business_profile_phone p LEFT JOIN business_profile b ON b.id = p.business_profile_id
                WHERE p.id = (SELECT MIN(m.id) FROM business_profile_phone m WHERE m.business_profile_id = b.id)
            )'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
