<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200112184510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE url url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE slot CHANGE student_id student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD training_id INT NOT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649BEFD98D1 ON user (training_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE slot CHANGE student_id student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BEFD98D1');
        $this->addSql('DROP INDEX IDX_8D93D649BEFD98D1 ON user');
        $this->addSql('ALTER TABLE user DROP training_id, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
