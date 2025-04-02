<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401185018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lease ADD rent_amount NUMERIC(6, 2) DEFAULT NULL, ADD parking_amount NUMERIC(6, 2) DEFAULT NULL, ADD fee NUMERIC(6, 2) DEFAULT NULL, ADD fee_type VARCHAR(255) NOT NULL, ADD various_amount NUMERIC(6, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lease DROP rent_amount, DROP parking_amount, DROP fee, DROP fee_type, DROP various_amount');
    }
}
