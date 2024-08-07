<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807133653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_group ADD test_group_id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'Testgroup ID\', DROP id, ADD PRIMARY KEY (test_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_group MODIFY test_group_id INT UNSIGNED NOT NULL COMMENT \'Testgroup ID\'');
        $this->addSql('DROP INDEX `primary` ON test_group');
        $this->addSql('ALTER TABLE test_group ADD id INT UNSIGNED NOT NULL COMMENT \'Testgroup ID\', DROP test_group_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
