<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903224702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commerce (id INT AUTO_INCREMENT NOT NULL, commercant_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BBF5FDF983FA6DD0 (commercant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, commerce_id INT DEFAULT NULL, user_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_14B78418B09114B7 (commerce_id), INDEX IDX_14B78418A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, commerce_id INT NOT NULL, date_arrivee DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_depart DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', nombre_adultes INT DEFAULT NULL, nombre_enfants INT DEFAULT NULL, nombre_chambres INT NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C84955B09114B7 (commerce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commerce ADD CONSTRAINT FK_BBF5FDF983FA6DD0 FOREIGN KEY (commercant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418B09114B7 FOREIGN KEY (commerce_id) REFERENCES commerce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B09114B7 FOREIGN KEY (commerce_id) REFERENCES commerce (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commerce DROP FOREIGN KEY FK_BBF5FDF983FA6DD0');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418B09114B7');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955B09114B7');
        $this->addSql('DROP TABLE commerce');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
