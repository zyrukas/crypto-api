<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200131224833 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, token VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, uid VARCHAR(32) DEFAULT NULL, label VARCHAR(255) NOT NULL, currency VARCHAR(4) NOT NULL, value NUMERIC(41, 20) UNSIGNED NOT NULL, value_in_usd NUMERIC(21, 2) UNSIGNED DEFAULT NULL, INDEX IDX_2AF5A5CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        $this->addSql('INSERT INTO `user` VALUES (1, \'demo1\', \'3edec063b94637eb1cc4395aa5abab91\');
                        INSERT INTO `user` VALUES (2, \'demo2\', \'3edec063b94637eb1cc4395aa5abab92\');
                        INSERT INTO `user` VALUES (3, \'demo3\', \'3edec063b94637eb1cc4395aa5abab93\');
                        INSERT INTO `user` VALUES (4, \'demo4\', \'3edec063b94637eb1cc4395aa5abab94\');
                        INSERT INTO `user` VALUES (5, \'demo5\', \'3edec063b94637eb1cc4395aa5abab95\');');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CA76ED395');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE asset');
    }
}
