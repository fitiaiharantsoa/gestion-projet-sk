<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250731095400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_file DROP uploaded_at');
        $this->addSql('ALTER TABLE project_file ALTER project_id DROP NOT NULL');
        $this->addSql('ALTER TABLE project_file ALTER type TYPE VARCHAR(100)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_file ADD uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE project_file ALTER project_id SET NOT NULL');
        $this->addSql('ALTER TABLE project_file ALTER type TYPE VARCHAR(50)');
        $this->addSql('COMMENT ON COLUMN project_file.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
