<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706175537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id SERIAL NOT NULL, responsable_id INT DEFAULT NULL, titre VARCHAR(200) NOT NULL, description TEXT DEFAULT NULL, bu VARCHAR(100) DEFAULT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, statut VARCHAR(50) NOT NULL, categorie VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE53C59D72 ON project (responsable_id)');
        $this->addSql('CREATE TABLE task (id SERIAL NOT NULL, assigne_id INT DEFAULT NULL, project_id INT DEFAULT NULL, titre VARCHAR(200) NOT NULL, description TEXT DEFAULT NULL, statut VARCHAR(50) NOT NULL, date_echeance DATE DEFAULT NULL, priorite VARCHAR(20) DEFAULT NULL, progression INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB258E7B8AB0 ON task (assigne_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE53C59D72 FOREIGN KEY (responsable_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258E7B8AB0 FOREIGN KEY (assigne_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE53C59D72');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB258E7B8AB0');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25166D1F9C');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE task');
    }
}
