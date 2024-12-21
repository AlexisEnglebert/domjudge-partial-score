<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221222501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scorecache ADD score_public INT UNSIGNED DEFAULT 0 NOT NULL COMMENT \'Public score\', ADD score_restricted INT UNSIGNED DEFAULT 0 NOT NULL COMMENT \'Restricted score\', DROP score');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scorecache ADD score INT UNSIGNED DEFAULT 0 NOT NULL COMMENT \'Score\', DROP score_public, DROP score_restricted');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
