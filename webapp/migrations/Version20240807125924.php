<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807125924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test_group CHANGE score score INT DEFAULT NULL COMMENT \'testgroup score\'');
        $this->addSql('ALTER TABLE testcase ADD testgroupid INT DEFAULT NULL COMMENT \'Test Group of the test-case\', DROP testgroup');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE testcase ADD testgroup VARCHAR(255) DEFAULT NULL COMMENT \'Test Group of the test-case\', DROP testgroupid');
        $this->addSql('ALTER TABLE test_group CHANGE score score INT NOT NULL COMMENT \'testgroup score\'');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
