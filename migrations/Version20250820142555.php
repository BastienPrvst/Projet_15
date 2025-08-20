<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250820142555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_identifier_email');
        $this->addSql('ALTER TABLE "user" ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER blocked DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER name DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER name TYPE VARCHAR(180)');
        $this->addSql('ALTER TABLE "user" ALTER blocked SET DEFAULT false');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');
    }
}
