<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105085306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry ADD tenant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775F9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('CREATE INDEX IDX_5A40775F9033212A ON financial_entry (tenant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775F9033212A');
        $this->addSql('DROP INDEX IDX_5A40775F9033212A ON financial_entry');
        $this->addSql('ALTER TABLE financial_entry DROP tenant_id');
    }
}
