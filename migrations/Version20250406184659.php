<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406184659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lease ADD bank_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lease ADD CONSTRAINT FK_E6C7749511C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id)');
        $this->addSql('CREATE INDEX IDX_E6C7749511C8FB41 ON lease (bank_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lease DROP FOREIGN KEY FK_E6C7749511C8FB41');
        $this->addSql('DROP INDEX IDX_E6C7749511C8FB41 ON lease');
        $this->addSql('ALTER TABLE lease DROP bank_id');
    }
}
