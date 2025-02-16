<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215220850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE upload_file ADD property_id INT NOT NULL');
        $this->addSql('ALTER TABLE upload_file ADD CONSTRAINT FK_81BB169549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('CREATE INDEX IDX_81BB169549213EC ON upload_file (property_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE upload_file DROP FOREIGN KEY FK_81BB169549213EC');
        $this->addSql('DROP INDEX IDX_81BB169549213EC ON upload_file');
        $this->addSql('ALTER TABLE upload_file DROP property_id');
    }
}
