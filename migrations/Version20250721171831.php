<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250721171831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les colonnes prenom et nom (nullable) à la table user';
    }

    public function up(Schema $schema): void
    {
        // Ajout des colonnes prenom et nom en nullable
        $this->addSql('ALTER TABLE "user" ADD prenom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD nom VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Suppression des colonnes en cas de rollback
        $this->addSql('ALTER TABLE "user" DROP prenom');
        $this->addSql('ALTER TABLE "user" DROP nom');
    }
}
