<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406061045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mortgage (id INT AUTO_INCREMENT NOT NULL, bank_id INT NOT NULL, property_id INT NOT NULL, from_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', to_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', billing_period VARCHAR(255) NOT NULL, mortgage_type VARCHAR(255) NOT NULL, INDEX IDX_E10ABAD011C8FB41 (bank_id), INDEX IDX_E10ABAD0549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mortgage ADD CONSTRAINT FK_E10ABAD011C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id)');
        $this->addSql('ALTER TABLE mortgage ADD CONSTRAINT FK_E10ABAD0549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property DROP mortgage_rate, DROP mortgage_type, DROP mortgage_end_date, DROP mortgage_rate2, DROP mortgage_type2, DROP mortgage_end_date2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mortgage DROP FOREIGN KEY FK_E10ABAD011C8FB41');
        $this->addSql('ALTER TABLE mortgage DROP FOREIGN KEY FK_E10ABAD0549213EC');
        $this->addSql('DROP TABLE mortgage');
        $this->addSql('ALTER TABLE property ADD mortgage_rate NUMERIC(10, 2) DEFAULT NULL, ADD mortgage_type VARCHAR(255) DEFAULT NULL, ADD mortgage_end_date DATE DEFAULT NULL, ADD mortgage_rate2 NUMERIC(10, 2) DEFAULT NULL, ADD mortgage_type2 VARCHAR(255) DEFAULT NULL, ADD mortgage_end_date2 DATE DEFAULT NULL');
    }
}
