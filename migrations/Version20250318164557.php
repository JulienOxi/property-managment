<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318164557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6549213EC');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F69033212A');
        $this->addSql('DROP INDEX IDX_1D60F2F6549213EC ON property_rent');
        $this->addSql('DROP INDEX IDX_1D60F2F69033212A ON property_rent');
        $this->addSql('ALTER TABLE property_rent DROP property_id, DROP tenant_id');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462549213EC');
        $this->addSql('DROP INDEX IDX_4E59C462549213EC ON tenant');
        $this->addSql('ALTER TABLE tenant DROP property_id, DROP rental_start_date, DROP rental_end_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_rent ADD property_id INT DEFAULT NULL, ADD tenant_id INT NOT NULL');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F69033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1D60F2F6549213EC ON property_rent (property_id)');
        $this->addSql('CREATE INDEX IDX_1D60F2F69033212A ON property_rent (tenant_id)');
        $this->addSql('ALTER TABLE tenant ADD property_id INT NOT NULL, ADD rental_start_date DATE NOT NULL, ADD rental_end_date DATE NOT NULL');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4E59C462549213EC ON tenant (property_id)');
    }
}
