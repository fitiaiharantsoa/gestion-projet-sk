<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708124054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_file (id SERIAL NOT NULL, projet_id INT NOT NULL, type VARCHAR(50) NOT NULL, date_upload TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B50EFE08C18272 ON project_file (projet_id)');
        $this->addSql('COMMENT ON COLUMN project_file.date_upload IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT FK_B50EFE08C18272 FOREIGN KEY (projet_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ALTER responsable_id SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER titre TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE project ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER bu SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER bu TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE project ALTER date_debut TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE project ALTER date_debut SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER date_fin TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE project ALTER date_fin SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER statut TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE project ALTER categorie SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER categorie TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_file DROP CONSTRAINT FK_B50EFE08C18272');
        $this->addSql('DROP TABLE project_file');
        $this->addSql('ALTER TABLE project ALTER responsable_id DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER titre TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE project ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER bu DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER bu TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE project ALTER date_debut TYPE DATE');
        $this->addSql('ALTER TABLE project ALTER date_debut DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER date_fin TYPE DATE');
        $this->addSql('ALTER TABLE project ALTER date_fin DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER statut TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE project ALTER categorie DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER categorie TYPE VARCHAR(50)');
    }
}
