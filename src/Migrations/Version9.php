<?php

namespace Chash\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Manages the migration to version 1.9.0.
 */
class Version9 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $sql = 'UPDATE settings_current SET selected_value = "1.9.0.18715"
                WHERE variable = "chamilo_database_version"';
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $sql = 'UPDATE settings_current SET selected_value = "1.8.8.14911"
                WHERE variable = "chamilo_database_version"';
        $this->addSql($sql);
    }
}
