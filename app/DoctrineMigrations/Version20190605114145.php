<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20190605114145 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            INSERT INTO config (key, title, value, format, description, created_at, updated_at, is_active)
            VALUES (
                \'REPORT_PROBLEM_EMAIL_ADDRESS\',
                \'Report a problem email address\',
                \'Digital@infopaginas.com\', \'text\',
                \'Users reports a problem will be sent to this email\',
                NOW(),
                NOW(),
                true
            ),
            (
                \'REPORT_PROBLEM_EMAIL_SUBJECT\',
                \'Report a problem subject\',
                \'Reporta un problema\',
                \'text\',
                \'Subject of report a problem email\',
                NOW(),
                NOW(),
                true
            )'
        );
    }

    public function down(Schema $schema)
    {

    }
}
