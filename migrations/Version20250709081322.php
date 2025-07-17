<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709081322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id SERIAL NOT NULL, created_by_id INT NOT NULL, message TEXT NOT NULL, seen BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAB03A8386 ON notification (created_by_id)');
        $this->addSql('CREATE TABLE project_log (id SERIAL NOT NULL, user_ref_id INT NOT NULL, project_id INT NOT NULL, action VARCHAR(255) NOT NULL, performed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D44B22644E55A94 ON project_log (user_ref_id)');
        $this->addSql('CREATE INDEX IDX_1D44B226166D1F9C ON project_log (project_id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_log ADD CONSTRAINT FK_1D44B22644E55A94 FOREIGN KEY (user_ref_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_log ADD CONSTRAINT FK_1D44B226166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAB03A8386');
        $this->addSql('ALTER TABLE project_log DROP CONSTRAINT FK_1D44B22644E55A94');
        $this->addSql('ALTER TABLE project_log DROP CONSTRAINT FK_1D44B226166D1F9C');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE project_log');
    }
}
