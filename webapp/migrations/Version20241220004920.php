<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241220004920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rankcache ADD totalscore_public INT DEFAULT 0 NOT NULL COMMENT \'Total score (public)\', ADD totalscore_restricted INT DEFAULT 0 NOT NULL COMMENT \'Total score (restricted)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rankcache DROP totalscore_public, DROP totalscore_restricted');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
