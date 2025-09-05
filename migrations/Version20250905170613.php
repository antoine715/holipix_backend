<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250905170613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE commerce DROP INDEX IDX_BBF5FDF983FA6DD0, ADD UNIQUE INDEX UNIQ_BBF5FDF983FA6DD0 (commercant_id)');
        $this->addSql('ALTER TABLE commerce DROP description, CHANGE phone phone VARCHAR(255) NOT NULL, CHANGE phone_fixe phone_fixe VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commerce ADD CONSTRAINT FK_BBF5FDF983FA6DD0 FOREIGN KEY (commercant_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76A76ED395');
        $this->addSql('ALTER TABLE commerce DROP INDEX UNIQ_BBF5FDF983FA6DD0, ADD INDEX IDX_BBF5FDF983FA6DD0 (commercant_id)');
        $this->addSql('ALTER TABLE commerce DROP FOREIGN KEY FK_BBF5FDF983FA6DD0');
        $this->addSql('ALTER TABLE commerce ADD description LONGTEXT DEFAULT NULL, CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE phone_fixe phone_fixe VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5A76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE users DROP verified_at');
    }
}
