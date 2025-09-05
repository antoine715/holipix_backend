<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250905092439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, role VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_880E0D76A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_phare (id INT AUTO_INCREMENT NOT NULL, commerce_id INT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_61F8ED83B09114B7 (commerce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, code VARCHAR(5) NOT NULL, name VARCHAR(50) NOT NULL, INDEX IDX_D4DB71B5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, reservation_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, paid_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6D28840DA76ED395 (user_id), UNIQUE INDEX UNIQ_6D28840DB83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, commerce_id INT DEFAULT NULL, rating INT NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C6B09114B7 (commerce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, date_form DATE DEFAULT NULL, date_to DATE DEFAULT NULL, adults INT DEFAULT NULL, children INT DEFAULT NULL, rooms INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validation (id INT AUTO_INCREMENT NOT NULL, photo_id INT NOT NULL, admin_id INT NOT NULL, status VARCHAR(20) NOT NULL, validated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_16AC5B6E7E9E4C8C (photo_id), INDEX IDX_16AC5B6E642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE feature_phare ADD CONSTRAINT FK_61F8ED83B09114B7 FOREIGN KEY (commerce_id) REFERENCES commerce (id)');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6B09114B7 FOREIGN KEY (commerce_id) REFERENCES commerce (id)');
        $this->addSql('ALTER TABLE validation ADD CONSTRAINT FK_16AC5B6E7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE validation ADD CONSTRAINT FK_16AC5B6E642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76A76ED395');
        $this->addSql('ALTER TABLE feature_phare DROP FOREIGN KEY FK_61F8ED83B09114B7');
        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5A76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DB83297E7');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6B09114B7');
        $this->addSql('ALTER TABLE validation DROP FOREIGN KEY FK_16AC5B6E7E9E4C8C');
        $this->addSql('ALTER TABLE validation DROP FOREIGN KEY FK_16AC5B6E642B8210');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE feature_phare');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE search');
        $this->addSql('DROP TABLE validation');
    }
}
