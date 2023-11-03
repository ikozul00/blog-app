<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103102932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE created_at created_at VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post CHANGE created_at created_at VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post RENAME INDEX idx_5a8a6c8d9d86650f TO IDX_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE post_tag RENAME INDEX idx_5ace3af0e85f12b8 TO IDX_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag RENAME INDEX idx_5ace3af05da88751 TO IDX_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64988987678');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user RENAME INDEX idx_8d93d64988987678 TO IDX_8D93D649D60322AC');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post RENAME INDEX idx_5a8a6c8da76ed395 TO IDX_5A8A6C8D9D86650F');
        $this->addSql('ALTER TABLE post_tag RENAME INDEX idx_5ace3af04b89032c TO IDX_5ACE3AF0E85F12B8');
        $this->addSql('ALTER TABLE post_tag RENAME INDEX idx_5ace3af0bad26311 TO IDX_5ACE3AF05DA88751');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64988987678 FOREIGN KEY (role_id) REFERENCES role (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user RENAME INDEX idx_8d93d649d60322ac TO IDX_8D93D64988987678');
    }
}
