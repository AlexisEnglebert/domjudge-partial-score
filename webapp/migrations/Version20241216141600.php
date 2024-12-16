<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216141600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scorecache ADD is_partially_accepted_restricted TINYINT(1) DEFAULT 0 NOT NULL COMMENT \'Has there been a partially accepted submission? (restricted audience)\', ADD is_partially_accepted_public TINYINT(1) DEFAULT 0 NOT NULL COMMENT \'Has there been a partially accepted submission? (public)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scorecache DROP is_partially_accepted_restricted, DROP is_partially_accepted_public');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
