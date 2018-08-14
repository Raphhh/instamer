<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180821090008 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE following (id INT UNSIGNED AUTO_INCREMENT NOT NULL, account_id INT UNSIGNED NOT NULL, pk VARCHAR(191) NOT NULL, username VARCHAR(191) NOT NULL, creation_datetime DATETIME NOT NULL, deletion_datetime DATETIME DEFAULT NULL, is_frozen TINYINT(1) NOT NULL, is_reciprocal TINYINT(1) NOT NULL, INDEX IDX_71BF8DE39B6B5FBA (account_id), UNIQUE INDEX username (account_id, username), UNIQUE INDEX account_id (account_id, pk), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE39B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE following');
    }
}
