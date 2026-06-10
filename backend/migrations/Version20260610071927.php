<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610071927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE eleccion_fantasy (id INT AUTO_INCREMENT NOT NULL, puntos_obtenidos INT DEFAULT NULL, creado_en DATETIME DEFAULT NULL, actualizado_en DATETIME DEFAULT NULL, usuario_fantasy_id INT DEFAULT NULL, partido_id INT DEFAULT NULL, jugador_id INT DEFAULT NULL, equipo_id INT DEFAULT NULL, INDEX IDX_D27E4B8EA8F03061 (usuario_fantasy_id), INDEX IDX_D27E4B8E11856EB4 (partido_id), INDEX IDX_D27E4B8EB8A54D43 (jugador_id), INDEX IDX_D27E4B8E23BFBED (equipo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8EA8F03061 FOREIGN KEY (usuario_fantasy_id) REFERENCES usuario_fantasy (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8E11856EB4 FOREIGN KEY (partido_id) REFERENCES partido (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8EB8A54D43 FOREIGN KEY (jugador_id) REFERENCES jugador (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8E23BFBED FOREIGN KEY (equipo_id) REFERENCES equipo (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8EA8F03061');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8E11856EB4');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8EB8A54D43');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8E23BFBED');
        $this->addSql('DROP TABLE eleccion_fantasy');
    }
}
