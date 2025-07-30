<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730055015 extends AbstractMigration
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
        $this->addSql('CREATE TABLE user_trusted_device (id SERIAL NOT NULL, owner_id INT NOT NULL, device_token VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_a TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_agent VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98FB50227E3C61F9 ON user_trusted_device (owner_id)');
        $this->addSql('COMMENT ON COLUMN user_trusted_device.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_trusted_device.expires_a IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63150A48F1 FOREIGN KEY (chef_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_trusted_device ADD CONSTRAINT FK_98FB50227E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD recipient_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BF5476CAE92F8F78 ON notification (recipient_id)');
        $this->addSql('ALTER TABLE project ADD departement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE53C59D73 FOREIGN KEY (responsable_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EECCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2FB3D0EECCF9E01E ON project (departement_id)');
        $this->addSql('ALTER TABLE project_file DROP CONSTRAINT fk_b50efe08c18272');
        $this->addSql('DROP INDEX idx_b50efe08c18272');
        $this->addSql('ALTER TABLE project_file ADD url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project_file ADD filename VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project_file ADD uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE project_file RENAME COLUMN projet_id TO project_id');
        $this->addSql('COMMENT ON COLUMN project_file.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT FK_B50EFE08166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B50EFE08166D1F9C ON project_file (project_id)');
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
        $this->addSql('ALTER TABLE task ADD createur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ALTER assigne_id SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER project_id SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER titre TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE task ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER progression SET DEFAULT 0');
        $this->addSql('ALTER TABLE task ALTER progression SET NOT NULL');
        $this->addSql('ALTER TABLE task ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE task ALTER updated_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2573A201E5 FOREIGN KEY (createur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB2573A201E5 ON task (createur_id)');
        $this->addSql('ALTER TABLE "user" ADD departement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD is_email_auth_enabled BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD auth_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD prenom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649CCF9E01E ON "user" (departement_id)');
        $this->addSql('ALTER INDEX uniq_identifier_email RENAME TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EECCF9E01E');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649CCF9E01E');
        $this->addSql('ALTER TABLE departement DROP CONSTRAINT FK_C1765B63150A48F1');
        $this->addSql('ALTER TABLE user_trusted_device DROP CONSTRAINT FK_98FB50227E3C61F9');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE user_trusted_device');
        $this->addSql('ALTER TABLE project_file DROP CONSTRAINT FK_B50EFE08166D1F9C');
        $this->addSql('DROP INDEX IDX_B50EFE08166D1F9C');
        $this->addSql('ALTER TABLE project_file DROP url');
        $this->addSql('ALTER TABLE project_file DROP filename');
        $this->addSql('ALTER TABLE project_file DROP uploaded_at');
        $this->addSql('ALTER TABLE project_file RENAME COLUMN project_id TO projet_id');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT fk_b50efe08c18272 FOREIGN KEY (projet_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b50efe08c18272 ON project_file (projet_id)');
        $this->addSql('DROP INDEX IDX_8D93D649CCF9E01E');
        $this->addSql('ALTER TABLE "user" DROP departement_id');
        $this->addSql('ALTER TABLE "user" DROP is_email_auth_enabled');
        $this->addSql('ALTER TABLE "user" DROP auth_code');
        $this->addSql('ALTER TABLE "user" DROP nom');
        $this->addSql('ALTER TABLE "user" DROP prenom');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO uniq_identifier_email');
        $this->addSql('ALTER TABLE project_log DROP CONSTRAINT FK_1D44B226A76ED395');
        $this->addSql('DROP INDEX IDX_1D44B226A76ED395');
        $this->addSql('ALTER TABLE project_log ADD user_ref_id INT NOT NULL');
        $this->addSql('ALTER TABLE project_log ADD performed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE project_log DROP user_id');
        $this->addSql('ALTER TABLE project_log DROP created_at');
        $this->addSql('ALTER TABLE project_log RENAME COLUMN message TO action');
        $this->addSql('ALTER TABLE project_log ADD CONSTRAINT fk_1d44b22644e55a94 FOREIGN KEY (user_ref_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1d44b22644e55a94 ON project_log (user_ref_id)');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAE92F8F78');
        $this->addSql('DROP INDEX IDX_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification DROP recipient_id');
        $this->addSql('ALTER TABLE notification ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS NULL');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE53C59D73');
        $this->addSql('DROP INDEX IDX_2FB3D0EECCF9E01E');
        $this->addSql('ALTER TABLE project DROP departement_id');
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
