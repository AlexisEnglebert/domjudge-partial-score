<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807123430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_group ADD probid INT UNSIGNED DEFAULT NULL COMMENT \'Problem ID\', ADD aggregation VARCHAR(255) NOT NULL COMMENT \'testgroup aggregation\'');
        $this->addSql('ALTER TABLE test_group ADD CONSTRAINT FK_661EE2CEEF049279 FOREIGN KEY (probid) REFERENCES problem (probid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_661EE2CEEF049279 ON test_group (probid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_group DROP FOREIGN KEY FK_661EE2CEEF049279');
        $this->addSql('DROP INDEX IDX_661EE2CEEF049279 ON test_group');
        $this->addSql('ALTER TABLE test_group DROP probid, DROP aggregation');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
