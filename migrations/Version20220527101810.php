<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527101810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author ADD created DATETIME NOT NULL, ADD changed DATETIME NOT NULL, ADD idUsersCreator INT DEFAULT NULL, ADD idUsersChanger INT DEFAULT NULL');
        $this->addSql('ALTER TABLE author ADD CONSTRAINT FK_BDAFD8C8DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE author ADD CONSTRAINT FK_BDAFD8C830D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_BDAFD8C8DBF11E1D ON author (idUsersCreator)');
        $this->addSql('CREATE INDEX IDX_BDAFD8C830D07CD5 ON author (idUsersChanger)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author DROP FOREIGN KEY FK_BDAFD8C8DBF11E1D');
        $this->addSql('ALTER TABLE author DROP FOREIGN KEY FK_BDAFD8C830D07CD5');
        $this->addSql('DROP INDEX IDX_BDAFD8C8DBF11E1D ON author');
        $this->addSql('DROP INDEX IDX_BDAFD8C830D07CD5 ON author');
        $this->addSql('ALTER TABLE author DROP created, DROP changed, DROP idUsersCreator, DROP idUsersChanger');
    }
}
