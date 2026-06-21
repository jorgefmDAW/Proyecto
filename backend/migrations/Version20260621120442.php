<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260621120442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE noticia (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(255) NOT NULL, categoria VARCHAR(255) NOT NULL, texto LONGTEXT NOT NULL, fecha DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE partido (id INT AUTO_INCREMENT NOT NULL, dia VARCHAR(255) NOT NULL, hora TIME NOT NULL, local_goles INT NOT NULL, visitante_goles INT NOT NULL, local_id INT NOT NULL, visitante_id INT NOT NULL, jornada_id INT DEFAULT NULL, INDEX IDX_4E79750B5D5A2101 (local_id), INDEX IDX_4E79750BD80AA8AF (visitante_id), INDEX IDX_4E79750B26E992D9 (jornada_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE puntuacion (id INT AUTO_INCREMENT NOT NULL, puntos INT NOT NULL, jugador_id INT DEFAULT NULL, jornada_id INT DEFAULT NULL, INDEX IDX_ABF67C3FB8A54D43 (jugador_id), INDEX IDX_ABF67C3F26E992D9 (jornada_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE refresh_tokens (refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE solicitud (id INT AUTO_INCREMENT NOT NULL, mensaje VARCHAR(255) NOT NULL, fecha DATETIME NOT NULL, aceptada TINYINT NOT NULL, liga_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_96D27CC0CF098064 (liga_id), INDEX IDX_96D27CC0DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(40) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, liga_seleccionada_id INT DEFAULT NULL, INDEX IDX_2265B05DAD3EF5BD (liga_seleccionada_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuario_fantasy (id INT AUTO_INCREMENT NOT NULL, puntos_totales INT NOT NULL, creador TINYINT NOT NULL, usuario_id INT DEFAULT NULL, liga_id INT DEFAULT NULL, INDEX IDX_69056C44DB38439E (usuario_id), INDEX IDX_69056C44CF098064 (liga_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750B5D5A2101 FOREIGN KEY (local_id) REFERENCES equipo (id)');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750BD80AA8AF FOREIGN KEY (visitante_id) REFERENCES equipo (id)');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750B26E992D9 FOREIGN KEY (jornada_id) REFERENCES jornada (id)');
        $this->addSql('ALTER TABLE puntuacion ADD CONSTRAINT FK_ABF67C3FB8A54D43 FOREIGN KEY (jugador_id) REFERENCES jugador (id)');
        $this->addSql('ALTER TABLE puntuacion ADD CONSTRAINT FK_ABF67C3F26E992D9 FOREIGN KEY (jornada_id) REFERENCES jornada (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE solicitud ADD CONSTRAINT FK_96D27CC0CF098064 FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('ALTER TABLE solicitud ADD CONSTRAINT FK_96D27CC0DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DAD3EF5BD FOREIGN KEY (liga_seleccionada_id) REFERENCES liga (id)');
        $this->addSql('ALTER TABLE usuario_fantasy ADD CONSTRAINT FK_69056C44DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario_fantasy ADD CONSTRAINT FK_69056C44CF098064 FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('DROP TABLE alineacion');
        $this->addSql('DROP TABLE eleccion_estrella');
        $this->addSql('ALTER TABLE chat_liga ADD CONSTRAINT FK_A12756E7DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE chat_liga ADD CONSTRAINT FK_A12756E7CF098064 FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8EA8F03061 FOREIGN KEY (usuario_fantasy_id) REFERENCES usuario_fantasy (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8E11856EB4 FOREIGN KEY (partido_id) REFERENCES partido (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8EB8A54D43 FOREIGN KEY (jugador_id) REFERENCES jugador (id)');
        $this->addSql('ALTER TABLE eleccion_fantasy ADD CONSTRAINT FK_D27E4B8E23BFBED FOREIGN KEY (equipo_id) REFERENCES equipo (id)');
        $this->addSql('ALTER TABLE foro_global ADD CONSTRAINT FK_A8CE5850DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE jornada ADD numero INT NOT NULL');
        $this->addSql('ALTER TABLE jugador ADD CONSTRAINT FK_527D6F1823BFBED FOREIGN KEY (equipo_id) REFERENCES equipo (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alineacion (id INT AUTO_INCREMENT NOT NULL, puntos_jornada INT NOT NULL, usuario_fantasy_id INT DEFAULT NULL, jornada_id INT DEFAULT NULL, INDEX IDX_A944AAC1A8F03061 (usuario_fantasy_id), INDEX IDX_A944AAC126E992D9 (jornada_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE eleccion_estrella (id INT AUTO_INCREMENT NOT NULL, alineacion_id INT NOT NULL, partido_id INT DEFAULT NULL, jugador_id INT DEFAULT NULL, INDEX IDX_1E0BA578D375E804 (alineacion_id), INDEX IDX_1E0BA57811856EB4 (partido_id), INDEX IDX_1E0BA578B8A54D43 (jugador_id), UNIQUE INDEX unique_partido_alineacion (alineacion_id, partido_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750B5D5A2101');
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750BD80AA8AF');
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750B26E992D9');
        $this->addSql('ALTER TABLE puntuacion DROP FOREIGN KEY FK_ABF67C3FB8A54D43');
        $this->addSql('ALTER TABLE puntuacion DROP FOREIGN KEY FK_ABF67C3F26E992D9');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE solicitud DROP FOREIGN KEY FK_96D27CC0CF098064');
        $this->addSql('ALTER TABLE solicitud DROP FOREIGN KEY FK_96D27CC0DB38439E');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DAD3EF5BD');
        $this->addSql('ALTER TABLE usuario_fantasy DROP FOREIGN KEY FK_69056C44DB38439E');
        $this->addSql('ALTER TABLE usuario_fantasy DROP FOREIGN KEY FK_69056C44CF098064');
        $this->addSql('DROP TABLE noticia');
        $this->addSql('DROP TABLE partido');
        $this->addSql('DROP TABLE puntuacion');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE solicitud');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE usuario_fantasy');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE chat_liga DROP FOREIGN KEY FK_A12756E7DB38439E');
        $this->addSql('ALTER TABLE chat_liga DROP FOREIGN KEY FK_A12756E7CF098064');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8EA8F03061');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8E11856EB4');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8EB8A54D43');
        $this->addSql('ALTER TABLE eleccion_fantasy DROP FOREIGN KEY FK_D27E4B8E23BFBED');
        $this->addSql('ALTER TABLE foro_global DROP FOREIGN KEY FK_A8CE5850DB38439E');
        $this->addSql('ALTER TABLE jornada DROP numero');
        $this->addSql('ALTER TABLE jugador DROP FOREIGN KEY FK_527D6F1823BFBED');
    }
}
