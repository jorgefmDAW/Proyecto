<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303132215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE puntuacion (id INT AUTO_INCREMENT NOT NULL, jornada INT NOT NULL, puntos INT NOT NULL, jugador_id INT DEFAULT NULL, INDEX IDX_ABF67C3FB8A54D43 (jugador_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE puntuacion ADD CONSTRAINT FK_ABF67C3FB8A54D43 FOREIGN KEY (jugador_id) REFERENCES jugador (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puntuacion DROP FOREIGN KEY FK_ABF67C3FB8A54D43');
        $this->addSql('DROP TABLE puntuacion');
    }
}
