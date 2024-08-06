<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806215945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_group (id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'Testgroup ID\', name VARCHAR(255) NOT NULL COMMENT \'testgroup name\', description LONGBLOB DEFAULT NULL COMMENT \'Description of this testgroup\', score INT NOT NULL COMMENT \'testgroup score\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Stores testgroup per problem\' ');
        $this->addSql('ALTER TABLE testcase ADD testgroup VARCHAR(255) DEFAULT NULL COMMENT \'Test Group of the test-case\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE test_group');
        $this->addSql('ALTER TABLE testcase DROP testgroup');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
