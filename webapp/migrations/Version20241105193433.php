<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105193433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE executable DROP zipfile');
        $this->addSql('ALTER TABLE problem CHANGE multipass_limit multipass_limit INT UNSIGNED DEFAULT NULL COMMENT \'Optional limit on the number of rounds; defaults to 1 for traditional problems, 2 for multi-pass problems if not specified.\'');
        $this->addSql('ALTER TABLE test_group DROP ranknumber');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE executable ADD zipfile LONGBLOB DEFAULT NULL COMMENT \'Zip file\'');
        $this->addSql('ALTER TABLE problem CHANGE multipass_limit multipass_limit INT UNSIGNED DEFAULT NULL COMMENT \'Optional limit on the number of rounds for multi-pass problems; defaults to 2 if not specified.\'');
        $this->addSql('ALTER TABLE test_group ADD ranknumber INT UNSIGNED NOT NULL COMMENT \'Determines order of the testgroup in judging\'');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
