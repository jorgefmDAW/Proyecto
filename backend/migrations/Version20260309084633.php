<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309084633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foro_global DROP FOREIGN KEY `FK_A8CE58507EB2C349`');
        $this->addSql('DROP INDEX IDX_A8CE58507EB2C349 ON foro_global');
        $this->addSql('ALTER TABLE foro_global CHANGE id_usuario_id usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE foro_global ADD CONSTRAINT FK_A8CE5850DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_A8CE5850DB38439E ON foro_global (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foro_global DROP FOREIGN KEY FK_A8CE5850DB38439E');
        $this->addSql('DROP INDEX IDX_A8CE5850DB38439E ON foro_global');
        $this->addSql('ALTER TABLE foro_global CHANGE usuario_id id_usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE foro_global ADD CONSTRAINT `FK_A8CE58507EB2C349` FOREIGN KEY (id_usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_A8CE58507EB2C349 ON foro_global (id_usuario_id)');
    }
}
