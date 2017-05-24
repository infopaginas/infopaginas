<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Domain\BusinessBundle\Entity\PaymentMethod;

class Version20170510144318 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $debitType = PaymentMethod::PAYMENT_METHOD_TYPE_DEBIT;
        $debitNameEn = 'Credit Card';
        $debitNameEs = 'Credito';

        $this->addSql("UPDATE payment_method SET name = '" . $debitNameEn . "' WHERE type = '" . $debitType . "'");
        $this->addSql(
            "UPDATE payment_method_translation SET content = '" . $debitNameEn . "'
            WHERE field = 'name' and locale = 'en' and object_id = (
            SELECT pm.id FROM payment_method pm WHERE pm.type = '" . $debitType . "')"
        );
        $this->addSql(
            "UPDATE payment_method_translation SET content = '" . $debitNameEs . "'
            WHERE field = 'name' and locale = 'es' and object_id = (
            SELECT pm.id FROM payment_method pm WHERE pm.type = '" . $debitType . "')"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
