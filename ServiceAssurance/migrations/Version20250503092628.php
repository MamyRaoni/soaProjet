<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503092628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE compagnie (id INT AUTO_INCREMENT NOT NULL, nom_compagnie VARCHAR(255) NOT NULL, attribut VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE police_assurance (id INT AUTO_INCREMENT NOT NULL, compagnie_id INT DEFAULT NULL, proprietaire_assurance VARCHAR(255) NOT NULL, beneficaire_assurance VARCHAR(255) NOT NULL, INDEX IDX_E214D4C752FBE437 (compagnie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE police_assurance ADD CONSTRAINT FK_E214D4C752FBE437 FOREIGN KEY (compagnie_id) REFERENCES compagnie (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE police_assurance DROP FOREIGN KEY FK_E214D4C752FBE437
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE compagnie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE police_assurance
        SQL);
    }
}
