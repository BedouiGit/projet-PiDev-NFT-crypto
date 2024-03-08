<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308091843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nft ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nft ADD CONSTRAINT FK_D9C7463C166D1F9C FOREIGN KEY (project_id) REFERENCES projets (id)');
        $this->addSql('CREATE INDEX IDX_D9C7463C166D1F9C ON nft (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nft DROP FOREIGN KEY FK_D9C7463C166D1F9C');
        $this->addSql('DROP INDEX IDX_D9C7463C166D1F9C ON nft');
        $this->addSql('ALTER TABLE nft DROP project_id');
    }
}
