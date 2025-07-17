<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250712193623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE departement (id SERIAL NOT NULL, chef_id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C1765B63150A48F1 ON departement (chef_id)');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63150A48F1 FOREIGN KEY (chef_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
       
        $this->addSql('ALTER TABLE project ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE53C59D72 FOREIGN KEY (responsable_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE53C59D72 ON project (responsable_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE departement DROP CONSTRAINT FK_C1765B63150A48F1');
        $this->addSql('DROP TABLE departement');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE53C59D72');
        $this->addSql('DROP INDEX IDX_2FB3D0EE53C59D72');
        $this->addSql('ALTER TABLE project DROP responsable_id');
        $this->addSql('ALTER TABLE project ALTER updated_at DROP NOT NULL');
    }
}
