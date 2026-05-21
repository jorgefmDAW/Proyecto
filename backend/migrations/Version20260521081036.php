<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521081036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE solicitud (id INT AUTO_INCREMENT NOT NULL, mensaje VARCHAR(255) NOT NULL, fecha DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE solicitud_liga (solicitud_id INT NOT NULL, liga_id INT NOT NULL, INDEX IDX_3B7480801CB9D6E4 (solicitud_id), INDEX IDX_3B748080CF098064 (liga_id), PRIMARY KEY (solicitud_id, liga_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE solicitud_usuario (solicitud_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_6F3C3DC1CB9D6E4 (solicitud_id), INDEX IDX_6F3C3DCDB38439E (usuario_id), PRIMARY KEY (solicitud_id, usuario_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE solicitud_liga ADD CONSTRAINT FK_3B7480801CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES solicitud (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE solicitud_liga ADD CONSTRAINT FK_3B748080CF098064 FOREIGN KEY (liga_id) REFERENCES liga (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE solicitud_usuario ADD CONSTRAINT FK_6F3C3DC1CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES solicitud (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE solicitud_usuario ADD CONSTRAINT FK_6F3C3DCDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE solicitud_liga DROP FOREIGN KEY FK_3B7480801CB9D6E4');
        $this->addSql('ALTER TABLE solicitud_liga DROP FOREIGN KEY FK_3B748080CF098064');
        $this->addSql('ALTER TABLE solicitud_usuario DROP FOREIGN KEY FK_6F3C3DC1CB9D6E4');
        $this->addSql('ALTER TABLE solicitud_usuario DROP FOREIGN KEY FK_6F3C3DCDB38439E');
        $this->addSql('DROP TABLE solicitud');
        $this->addSql('DROP TABLE solicitud_liga');
        $this->addSql('DROP TABLE solicitud_usuario');
    }
}
