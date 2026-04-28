<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309084438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE foro_global (id INT AUTO_INCREMENT NOT NULL, mensaje LONGTEXT NOT NULL, id_usuario_id INT DEFAULT NULL, INDEX IDX_A8CE58507EB2C349 (id_usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE foro_global ADD CONSTRAINT FK_A8CE58507EB2C349 FOREIGN KEY (id_usuario_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foro_global DROP FOREIGN KEY FK_A8CE58507EB2C349');
        $this->addSql('DROP TABLE foro_global');
    }
}
