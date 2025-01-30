<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130212404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8BF21CDEB03A8386 ON property (created_by_id)');
        $this->addSql('CREATE INDEX IDX_8BF21CDE896DBBDE ON property (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDEB03A8386');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE896DBBDE');
        $this->addSql('DROP INDEX IDX_8BF21CDEB03A8386 ON property');
        $this->addSql('DROP INDEX IDX_8BF21CDE896DBBDE ON property');
        $this->addSql('ALTER TABLE property DROP created_by_id, DROP updated_by_id, DROP created_at, DROP updated_at');
    }
}
