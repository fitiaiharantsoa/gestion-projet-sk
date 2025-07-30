<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728075248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_file DROP CONSTRAINT fk_b50efe08c18272');
        $this->addSql('DROP INDEX idx_b50efe08c18272');
        $this->addSql('ALTER TABLE project_file RENAME COLUMN projet_id TO project_id');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT FK_B50EFE08166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B50EFE08166D1F9C ON project_file (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_file DROP CONSTRAINT FK_B50EFE08166D1F9C');
        $this->addSql('DROP INDEX IDX_B50EFE08166D1F9C');
        $this->addSql('ALTER TABLE project_file RENAME COLUMN project_id TO projet_id');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT fk_b50efe08c18272 FOREIGN KEY (projet_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b50efe08c18272 ON project_file (projet_id)');
    }
}
