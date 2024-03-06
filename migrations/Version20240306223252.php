<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306223252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, contenu VARCHAR(255) DEFAULT NULL, auteur VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, phototag VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags_article (tags_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_2486E5778D7B4FB4 (tags_id), INDEX IDX_2486E5777294869C (article_id), PRIMARY KEY(tags_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tags_article ADD CONSTRAINT FK_2486E5778D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_article ADD CONSTRAINT FK_2486E5777294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA2843073');
        $this->addSql('ALTER TABLE commentaire CHANGE date_contenu date_contenu DATETIME NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA2843073 FOREIGN KEY (actualite_id) REFERENCES actualite (id)');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DB12469DE2');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tags_article DROP FOREIGN KEY FK_2486E5778D7B4FB4');
        $this->addSql('ALTER TABLE tags_article DROP FOREIGN KEY FK_2486E5777294869C');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_article');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA2843073');
        $this->addSql('ALTER TABLE commentaire CHANGE date_contenu date_contenu DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA2843073 FOREIGN KEY (actualite_id) REFERENCES actualite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DB12469DE2');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }
}
