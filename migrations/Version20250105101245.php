<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105101245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry ADD bank_id INT NOT NULL');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775F11C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id)');
        $this->addSql('CREATE INDEX IDX_5A40775F11C8FB41 ON financial_entry (bank_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775F11C8FB41');
        $this->addSql('DROP INDEX IDX_5A40775F11C8FB41 ON financial_entry');
        $this->addSql('ALTER TABLE financial_entry DROP bank_id');
    }
}
