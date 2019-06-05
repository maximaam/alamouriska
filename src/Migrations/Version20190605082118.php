<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605082118 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment_mail_queue (id INT AUTO_INCREMENT NOT NULL, post VARCHAR(32) NOT NULL, post_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', avatar_name VARCHAR(128) DEFAULT NULL, allow_member_contact TINYINT(1) NOT NULL, allow_post_notification TINYINT(1) NOT NULL, facebook_id VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), INDEX username_idx (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joke (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post LONGTEXT NOT NULL, description LONGTEXT NOT NULL, image_name VARCHAR(128) DEFAULT NULL, question TINYINT(1) NOT NULL, addr VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8D8563DDA76ED395 (user_id), INDEX created_at_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE journal (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, addr VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C1A7E74DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expression (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post VARCHAR(128) NOT NULL, description LONGTEXT NOT NULL, image_name VARCHAR(128) DEFAULT NULL, question TINYINT(1) NOT NULL, addr VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D8305601A76ED395 (user_id), INDEX created_at_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, owner VARCHAR(64) NOT NULL, owner_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D95F49C1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, addr VARCHAR(255) NOT NULL, rating SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, word_id INT DEFAULT NULL, expression_id INT DEFAULT NULL, proverb_id INT DEFAULT NULL, joke_id INT DEFAULT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9474526CE357438D (word_id), INDEX IDX_9474526CADBB65A1 (expression_id), INDEX IDX_9474526C9EE15F57 (proverb_id), INDEX IDX_9474526C30122C15 (joke_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post VARCHAR(128) NOT NULL, in_tamazight VARCHAR(255) DEFAULT NULL, in_arabic VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, image_name VARCHAR(128) DEFAULT NULL, question TINYINT(1) NOT NULL, addr VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C3F17511A76ED395 (user_id), INDEX created_at_idx (created_at), INDEX post_idx (post), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proverb (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post LONGTEXT NOT NULL, description LONGTEXT NOT NULL, image_name VARCHAR(128) DEFAULT NULL, question TINYINT(1) NOT NULL, addr VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9271AE88A76ED395 (user_id), INDEX created_at_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, alias VARCHAR(128) NOT NULL, description LONGTEXT NOT NULL, embedded TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deleted (id INT AUTO_INCREMENT NOT NULL, post LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, user_id INT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE joke ADD CONSTRAINT FK_8D8563DDA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE journal ADD CONSTRAINT FK_C1A7E74DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE expression ADD CONSTRAINT FK_D8305601A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE liking ADD CONSTRAINT FK_D95F49C1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CADBB65A1 FOREIGN KEY (expression_id) REFERENCES expression (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9EE15F57 FOREIGN KEY (proverb_id) REFERENCES proverb (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C30122C15 FOREIGN KEY (joke_id) REFERENCES joke (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE word ADD CONSTRAINT FK_C3F17511A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE proverb ADD CONSTRAINT FK_9271AE88A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE joke DROP FOREIGN KEY FK_8D8563DDA76ED395');
        $this->addSql('ALTER TABLE journal DROP FOREIGN KEY FK_C1A7E74DA76ED395');
        $this->addSql('ALTER TABLE expression DROP FOREIGN KEY FK_D8305601A76ED395');
        $this->addSql('ALTER TABLE liking DROP FOREIGN KEY FK_D95F49C1A76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE word DROP FOREIGN KEY FK_C3F17511A76ED395');
        $this->addSql('ALTER TABLE proverb DROP FOREIGN KEY FK_9271AE88A76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C30122C15');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CADBB65A1');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE357438D');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C9EE15F57');
        $this->addSql('DROP TABLE comment_mail_queue');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE joke');
        $this->addSql('DROP TABLE journal');
        $this->addSql('DROP TABLE expression');
        $this->addSql('DROP TABLE liking');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE word');
        $this->addSql('DROP TABLE proverb');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE deleted');
    }
}
