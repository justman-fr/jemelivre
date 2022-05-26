<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220525151540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, address JSON DEFAULT NULL, place_id VARCHAR(255) DEFAULT NULL, opening_hours JSON DEFAULT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_FDD2B5A8DBF11E1D (idUsersCreator), INDEX IDX_FDD2B5A830D07CD5 (idUsersChanger), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_settings ADD CONSTRAINT FK_FDD2B5A8DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE company_settings ADD CONSTRAINT FK_FDD2B5A830D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE company_settings');
    }
}
