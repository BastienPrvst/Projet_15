<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250814095946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
	    $passwordHash = $_ENV['MIGRATE_PASSWORD'] ?? null;

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10ca76ed395');
        $this->addSql('DROP INDEX idx_6a2ca10ca76ed395');
        $this->addSql('ALTER TABLE media RENAME COLUMN user_id TO "user"');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C8D93D649 FOREIGN KEY ("user") REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6A2CA10C8D93D649 ON media ("user")');
        $this->addSql('ALTER TABLE "user" ADD roles JSON DEFAULT NULL');
	    $this->addSql('UPDATE "user" SET roles = \'[]\' WHERE roles IS NULL');
        $this->addSql('ALTER TABLE "user" ADD password VARCHAR(255) NULL');
	    $this->addSql("UPDATE \"user\" SET password = '" . addslashes($passwordHash) . "' WHERE password IS NULL");
        $this->addSql('ALTER TABLE "user" DROP admin');
        $this->addSql('ALTER TABLE "user" ALTER name DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER name TYPE VARCHAR(180)');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO UNIQ_IDENTIFIER_EMAIL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10C8D93D649');
        $this->addSql('DROP INDEX IDX_6A2CA10C8D93D649');
        $this->addSql('ALTER TABLE media RENAME COLUMN "user" TO user_id');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT fk_6a2ca10ca76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6a2ca10ca76ed395 ON media (user_id)');
        $this->addSql('ALTER TABLE "user" ADD admin BOOLEAN DEFAULT false;');
        $this->addSql('ALTER TABLE "user" DROP roles');
        $this->addSql('ALTER TABLE "user" DROP password');
        $this->addSql('ALTER TABLE "user" ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER INDEX uniq_identifier_email RENAME TO uniq_8d93d649e7927c74');
    }
}
