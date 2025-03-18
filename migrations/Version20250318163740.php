<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318163740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lease (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, from_at DATE NOT NULL, to_at DATE NOT NULL, INDEX IDX_E6C77495549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lease ADD CONSTRAINT FK_E6C77495549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property_rent ADD lease_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6D3CA542C FOREIGN KEY (lease_id) REFERENCES lease (id)');
        $this->addSql('CREATE INDEX IDX_1D60F2F6D3CA542C ON property_rent (lease_id)');
        $this->addSql('ALTER TABLE tenant ADD lease_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462D3CA542C FOREIGN KEY (lease_id) REFERENCES lease (id)');
        $this->addSql('CREATE INDEX IDX_4E59C462D3CA542C ON tenant (lease_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6D3CA542C');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462D3CA542C');
        $this->addSql('ALTER TABLE lease DROP FOREIGN KEY FK_E6C77495549213EC');
        $this->addSql('DROP TABLE lease');
        $this->addSql('DROP INDEX IDX_1D60F2F6D3CA542C ON property_rent');
        $this->addSql('ALTER TABLE property_rent DROP lease_id');
        $this->addSql('DROP INDEX IDX_4E59C462D3CA542C ON tenant');
        $this->addSql('ALTER TABLE tenant DROP lease_id');
    }
}
