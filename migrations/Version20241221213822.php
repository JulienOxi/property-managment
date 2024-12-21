<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221213822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE financial_entry (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, created_by_id INT NOT NULL, type VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_paid TINYINT(1) NOT NULL, paid_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A40775F549213EC (property_id), INDEX IDX_5A40775FB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload_file (id INT AUTO_INCREMENT NOT NULL, updated_by_id INT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, entity_class VARCHAR(100) NOT NULL, entity_id INT NOT NULL, INDEX IDX_81BB169896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775F549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775FB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE upload_file ADD CONSTRAINT FK_81BB169896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775F549213EC');
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775FB03A8386');
        $this->addSql('ALTER TABLE upload_file DROP FOREIGN KEY FK_81BB169896DBBDE');
        $this->addSql('DROP TABLE financial_entry');
        $this->addSql('DROP TABLE upload_file');
    }
}
