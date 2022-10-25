<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221025134933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, qte INT NOT NULL, create_date_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', prix INT NOT NULL, image VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, objet VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', state TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307FA76ED395 (user_id), INDEX IDX_B6BD307FCFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, panier LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', total INT NOT NULL, createdate_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reference VARCHAR(255) NOT NULL, adress_livraison LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', adress_facturation LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', email VARCHAR(255) NOT NULL, name_livraison VARCHAR(255) NOT NULL, name_facturation VARCHAR(255) DEFAULT NULL, state TINYINT(1) DEFAULT NULL, state_sending VARCHAR(255) DEFAULT NULL, quantity INT DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCFFE9AD6');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
