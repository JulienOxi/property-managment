<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221210103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, name VARCHAR(50) NOT NULL, email VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, bic VARCHAR(11) NOT NULL, iban VARCHAR(34) NOT NULL, clearing_number SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D860BF7AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, address_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, purchase_price NUMERIC(10, 2) DEFAULT NULL, purchase_date DATE NOT NULL, mortgage_rate NUMERIC(10, 2) DEFAULT NULL, mortgage_type LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', mortgage_end_date DATE DEFAULT NULL, ewid VARCHAR(255) DEFAULT NULL, egid VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8BF21CDEF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_access_control (property_id INT NOT NULL, access_control_id INT NOT NULL, INDEX IDX_F33A2D5A549213EC (property_id), INDEX IDX_F33A2D5A18805F8F (access_control_id), PRIMARY KEY(property_id, access_control_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_rent (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, created_by_id INT NOT NULL, tenant_id INT NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, price NUMERIC(7, 2) NOT NULL, monthly_price NUMERIC(7, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ended_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1D60F2F6549213EC (property_id), INDEX IDX_1D60F2F6B03A8386 (created_by_id), INDEX IDX_1D60F2F69033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(50) DEFAULT NULL, rental_start_date DATE NOT NULL, rental_end_date DATE NOT NULL, INDEX IDX_4E59C462549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE property_access_control ADD CONSTRAINT FK_F33A2D5A549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_access_control ADD CONSTRAINT FK_F33A2D5A18805F8F FOREIGN KEY (access_control_id) REFERENCES access_control (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F69033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE access_control ADD property_id INT NOT NULL');
        $this->addSql('ALTER TABLE access_control ADD CONSTRAINT FK_25FEF65E549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('CREATE INDEX IDX_25FEF65E549213EC ON access_control (property_id)');
        $this->addSql('ALTER TABLE address ADD phone VARCHAR(25) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_control DROP FOREIGN KEY FK_25FEF65E549213EC');
        $this->addSql('ALTER TABLE bank DROP FOREIGN KEY FK_D860BF7AB03A8386');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDEF5B7AF75');
        $this->addSql('ALTER TABLE property_access_control DROP FOREIGN KEY FK_F33A2D5A549213EC');
        $this->addSql('ALTER TABLE property_access_control DROP FOREIGN KEY FK_F33A2D5A18805F8F');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6549213EC');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6B03A8386');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F69033212A');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462549213EC');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE property_access_control');
        $this->addSql('DROP TABLE property_rent');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP INDEX IDX_25FEF65E549213EC ON access_control');
        $this->addSql('ALTER TABLE access_control DROP property_id');
        $this->addSql('ALTER TABLE address DROP phone');
    }
}
