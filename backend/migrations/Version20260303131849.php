<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303131849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipo (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE jugador (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, equipo_id INT DEFAULT NULL, INDEX IDX_527D6F1823BFBED (equipo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE jugador ADD CONSTRAINT FK_527D6F1823BFBED FOREIGN KEY (equipo_id) REFERENCES equipo (id)');
        $this->addSql('DROP TABLE cursos');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cursos (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, categoria VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, descripcion VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, fecha_inicio DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE jugador DROP FOREIGN KEY FK_527D6F1823BFBED');
        $this->addSql('DROP TABLE equipo');
        $this->addSql('DROP TABLE jugador');
    }
}
