<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801123437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bu (id SERIAL NOT NULL, responsable_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_367D9E6953C59D72 ON bu (responsable_id)');
        $this->addSql('ALTER TABLE bu ADD CONSTRAINT FK_367D9E6953C59D72 FOREIGN KEY (responsable_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE departement ADD bu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63E0319FBC FOREIGN KEY (bu_id) REFERENCES bu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C1765B63E0319FBC ON departement (bu_id)');
        $this->addSql('ALTER TABLE project DROP bu');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE departement DROP CONSTRAINT FK_C1765B63E0319FBC');
        $this->addSql('ALTER TABLE bu DROP CONSTRAINT FK_367D9E6953C59D72');
        $this->addSql('DROP TABLE bu');
        $this->addSql('DROP INDEX IDX_C1765B63E0319FBC');
        $this->addSql('ALTER TABLE departement DROP bu_id');
        $this->addSql('ALTER TABLE project ADD bu VARCHAR(255) NOT NULL');
    }
}
