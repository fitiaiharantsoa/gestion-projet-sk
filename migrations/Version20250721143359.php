<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721143359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne createur_id avec une valeur par défaut NULL
        $this->addSql('ALTER TABLE task ADD createur_id INT DEFAULT NULL');
        
        // Mettre à jour les anciennes lignes avec un utilisateur par défaut (assure-toi que l'utilisateur avec l'ID 1 existe)
        $this->addSql('UPDATE task SET createur_id = 1 WHERE createur_id IS NULL');
        
        // Ajouter la contrainte de clé étrangère sur createur_id
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2573A201E5 FOREIGN KEY (createur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        
        // Créer l'index sur createur_id
        $this->addSql('CREATE INDEX IDX_527EDB2573A201E5 ON task (createur_id)');
        
        // Ajouter les contraintes aux autres colonnes de la table task
        $this->addSql('ALTER TABLE task ALTER assigne_id SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER project_id SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER titre TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE task ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER progression SET DEFAULT 0');
        $this->addSql('ALTER TABLE task ALTER progression SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at DROP NOT NULL');
        
        // Ajouter les commentaires pour les colonnes
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task.updated_at IS \'(DC2Type:datetime_immutable)\'');

        // Renommer l'index existant
        $this->addSql('ALTER INDEX uniq_identifier_email RENAME TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        // Annuler les changements effectués dans le up()
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO uniq_identifier_email');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB2573A201E5');
        $this->addSql('DROP INDEX IDX_527EDB2573A201E5');
        $this->addSql('ALTER TABLE task DROP createur_id');
        $this->addSql('ALTER TABLE task ALTER assigne_id DROP NOT NULL');
        $this->addSql('ALTER TABLE task ALTER project_id DROP NOT NULL');
        $this->addSql('ALTER TABLE task ALTER titre TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE task ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE task ALTER progression DROP DEFAULT');
        $this->addSql('ALTER TABLE task ALTER progression DROP NOT NULL');
        $this->addSql('ALTER TABLE task ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN task.created_at IS NULL');
        $this->addSql('COMMENT ON COLUMN task.updated_at IS NULL');
    }
}
