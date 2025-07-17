<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715092522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE trusted_device_id_seq CASCADE');
        $this->addSql('CREATE TABLE user_trusted_device (id SERIAL NOT NULL, owner_id INT NOT NULL, device_token VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_a TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_agent VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98FB50227E3C61F9 ON user_trusted_device (owner_id)');
        $this->addSql('COMMENT ON COLUMN user_trusted_device.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_trusted_device.expires_a IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_trusted_device ADD CONSTRAINT FK_98FB50227E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trusted_device DROP CONSTRAINT fk_f37e8f7b7e3c61f9');
        $this->addSql('DROP TABLE trusted_device');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE trusted_device_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE trusted_device (id SERIAL NOT NULL, owner_id INT NOT NULL, device_token VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_agent VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_f37e8f7b7e3c61f9 ON trusted_device (owner_id)');
        $this->addSql('COMMENT ON COLUMN trusted_device.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trusted_device.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE trusted_device ADD CONSTRAINT fk_f37e8f7b7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_trusted_device DROP CONSTRAINT FK_98FB50227E3C61F9');
        $this->addSql('DROP TABLE user_trusted_device');
    }
}
