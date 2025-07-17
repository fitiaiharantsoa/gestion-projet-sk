<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708072944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_comment (id SERIAL NOT NULL, task_id INT NOT NULL, utilisateur_id INT NOT NULL, message TEXT NOT NULL, date_commentaire TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B9578868DB60186 ON task_comment (task_id)');
        $this->addSql('CREATE INDEX IDX_8B957886FB88E14F ON task_comment (utilisateur_id)');
        $this->addSql('ALTER TABLE task_comment ADD CONSTRAINT FK_8B9578868DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_comment ADD CONSTRAINT FK_8B957886FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task_comment DROP CONSTRAINT FK_8B9578868DB60186');
        $this->addSql('ALTER TABLE task_comment DROP CONSTRAINT FK_8B957886FB88E14F');
        $this->addSql('DROP TABLE task_comment');
    }
}
