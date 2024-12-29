<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229190511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE financial_entry (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, created_by_id INT NOT NULL, type VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_paid TINYINT(1) NOT NULL, paid_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A40775F549213EC (property_id), INDEX IDX_5A40775FB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload_file (id INT AUTO_INCREMENT NOT NULL, updated_by_id INT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, entity_class VARCHAR(100) NOT NULL, entity_id INT NOT NULL, INDEX IDX_81BB169896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775F549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE financial_entry ADD CONSTRAINT FK_5A40775FB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE upload_file ADD CONSTRAINT FK_81BB169896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE access_control ADD CONSTRAINT FK_25FEF65E549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE property CHANGE mortgage_type mortgage_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE property_access_control ADD CONSTRAINT FK_F33A2D5A549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_access_control ADD CONSTRAINT FK_F33A2D5A18805F8F FOREIGN KEY (access_control_id) REFERENCES access_control (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F6B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE property_rent ADD CONSTRAINT FK_1D60F2F69033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE user ADD auth_code VARCHAR(255) DEFAULT NULL, ADD is_verified TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775F549213EC');
        $this->addSql('ALTER TABLE financial_entry DROP FOREIGN KEY FK_5A40775FB03A8386');
        $this->addSql('ALTER TABLE upload_file DROP FOREIGN KEY FK_81BB169896DBBDE');
        $this->addSql('DROP TABLE financial_entry');
        $this->addSql('DROP TABLE upload_file');
        $this->addSql('ALTER TABLE access_control DROP FOREIGN KEY FK_25FEF65E549213EC');
        $this->addSql('ALTER TABLE `user` DROP auth_code, DROP is_verified');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462549213EC');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDEF5B7AF75');
        $this->addSql('ALTER TABLE property CHANGE mortgage_type mortgage_type LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6549213EC');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F6B03A8386');
        $this->addSql('ALTER TABLE property_rent DROP FOREIGN KEY FK_1D60F2F69033212A');
        $this->addSql('ALTER TABLE bank DROP FOREIGN KEY FK_D860BF7AB03A8386');
        $this->addSql('ALTER TABLE property_access_control DROP FOREIGN KEY FK_F33A2D5A549213EC');
        $this->addSql('ALTER TABLE property_access_control DROP FOREIGN KEY FK_F33A2D5A18805F8F');
    }
}
