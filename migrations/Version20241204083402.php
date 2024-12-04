<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204083402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chatroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, _user_id INT NOT NULL, chatroom_id INT NOT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307FD32632E8 (_user_id), INDEX IDX_B6BD307FCAF8A031 (chatroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_chatroom (id INT AUTO_INCREMENT NOT NULL, _user_id INT NOT NULL, chatroom_id INT NOT NULL, last_read DATETIME DEFAULT NULL, INDEX IDX_FFB38AC4D32632E8 (_user_id), INDEX IDX_FFB38AC4CAF8A031 (chatroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD32632E8 FOREIGN KEY (_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCAF8A031 FOREIGN KEY (chatroom_id) REFERENCES chatroom (id)');
        $this->addSql('ALTER TABLE user_chatroom ADD CONSTRAINT FK_FFB38AC4D32632E8 FOREIGN KEY (_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_chatroom ADD CONSTRAINT FK_FFB38AC4CAF8A031 FOREIGN KEY (chatroom_id) REFERENCES chatroom (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FD32632E8');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCAF8A031');
        $this->addSql('ALTER TABLE user_chatroom DROP FOREIGN KEY FK_FFB38AC4D32632E8');
        $this->addSql('ALTER TABLE user_chatroom DROP FOREIGN KEY FK_FFB38AC4CAF8A031');
        $this->addSql('DROP TABLE chatroom');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_chatroom');
    }
}
