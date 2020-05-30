<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200530204614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games ADD run_at DATETIME DEFAULT NULL, ADD last DATETIME DEFAULT NULL, ADD tour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD grid LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE creator_id creator_id INT DEFAULT NULL, CHANGE options options JSON NOT NULL');
        $this->addSql('ALTER TABLE players ADD life SMALLINT UNSIGNED NOT NULL, ADD score SMALLINT UNSIGNED NOT NULL, ADD boats JSON DEFAULT NULL, CHANGE game_id game_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE team team SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP run_at, DROP last, DROP tour, DROP grid, CHANGE creator_id creator_id INT DEFAULT NULL, CHANGE options options LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE players DROP life, DROP score, DROP boats, CHANGE game_id game_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE team team SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
