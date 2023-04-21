<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230420213555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, modify_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cat (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modify_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, type_pet VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, moditfy_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_produit (category_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_EE7DAC5912469DE2 (category_id), INDEX IDX_EE7DAC59F347EFB (produit_id), PRIMARY KEY(category_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, cendres INT NOT NULL, eau INT NOT NULL, fibre INT NOT NULL, glucide INT NOT NULL, lipide INT NOT NULL, proteine INT NOT NULL, UNIQUE INDEX UNIQ_522FA950F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, urlimage VARCHAR(255) DEFAULT NULL, validate TINYINT(1) NOT NULL, sterilise TINYINT(1) NOT NULL, product_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, modify_at DATETIME DEFAULT NULL, INDEX IDX_29A5EC2744F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_produit ADD CONSTRAINT FK_EE7DAC5912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_produit ADD CONSTRAINT FK_EE7DAC59F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE characteristic ADD CONSTRAINT FK_522FA950F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2744F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_produit DROP FOREIGN KEY FK_EE7DAC5912469DE2');
        $this->addSql('ALTER TABLE category_produit DROP FOREIGN KEY FK_EE7DAC59F347EFB');
        $this->addSql('ALTER TABLE characteristic DROP FOREIGN KEY FK_522FA950F347EFB');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2744F5D008');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE cat');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_produit');
        $this->addSql('DROP TABLE characteristic');
        $this->addSql('DROP TABLE produit');
    }
}
