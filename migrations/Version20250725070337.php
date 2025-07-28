<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250725070337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_log DROP CONSTRAINT fk_1d44b22644e55a94');
        $this->addSql('DROP INDEX idx_1d44b22644e55a94');
        $this->addSql('ALTER TABLE project_log ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_log ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE project_log DROP user_ref_id');
        $this->addSql('ALTER TABLE project_log DROP performed_at');
        $this->addSql('ALTER TABLE project_log RENAME COLUMN action TO message');
        $this->addSql('COMMENT ON COLUMN project_log.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE project_log ADD CONSTRAINT FK_1D44B226A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1D44B226A76ED395 ON project_log (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_log DROP CONSTRAINT FK_1D44B226A76ED395');
        $this->addSql('DROP INDEX IDX_1D44B226A76ED395');
        $this->addSql('ALTER TABLE project_log ADD user_ref_id INT NOT NULL');
        $this->addSql('ALTER TABLE project_log ADD performed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE project_log DROP user_id');
        $this->addSql('ALTER TABLE project_log DROP created_at');
        $this->addSql('ALTER TABLE project_log RENAME COLUMN message TO action');
        $this->addSql('ALTER TABLE project_log ADD CONSTRAINT fk_1d44b22644e55a94 FOREIGN KEY (user_ref_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1d44b22644e55a94 ON project_log (user_ref_id)');
    }
}
