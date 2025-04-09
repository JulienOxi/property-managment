<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408164252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry ADD mortgage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775F15375FCD FOREIGN KEY (mortgage_id) REFERENCES mortgage (id)');
        $this->addSql('CREATE INDEX IDX_5A40775F15375FCD ON financial_entry (mortgage_id)');
        $this->addSql('ALTER TABLE mortgage ADD amount NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775F15375FCD');
        $this->addSql('DROP INDEX IDX_5A40775F15375FCD ON financial_entry');
        $this->addSql('ALTER TABLE financial_entry DROP mortgage_id');
        $this->addSql('ALTER TABLE mortgage DROP amount');
    }
}
