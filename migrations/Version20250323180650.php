<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323180650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6B03A8386');
        $this->addSql('DROP INDEX IDX_1D60F2F6B03A8386 ON property_rent');
        $this->addSql('ALTER TABLE property_rent DROP created_by_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_rent ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1D60F2F6B03A8386 ON property_rent (created_by_id)');
    }
}
