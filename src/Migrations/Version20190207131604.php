<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207131604 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mot ADD image_name VARCHAR(128) DEFAULT NULL, ADD image_file VARCHAR(255) NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE in_tamazight in_tamazight VARCHAR(255) DEFAULT NULL, CHANGE in_arabic in_arabic VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD avatar_name VARCHAR(128) DEFAULT NULL, ADD avatar_size INT NOT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP created_at, DROP updated_at, DROP avatar_name, DROP avatar_size, CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE mot DROP image_name, DROP image_file, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE in_tamazight in_tamazight VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE in_arabic in_arabic VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
