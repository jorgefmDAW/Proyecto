<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610071450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_liga (id INT AUTO_INCREMENT NOT NULL, mensaje LONGTEXT NOT NULL, fecha DATETIME NOT NULL, usuario_id INT DEFAULT NULL, liga_id INT DEFAULT NULL, INDEX IDX_A12756E7DB38439E (usuario_id), INDEX IDX_A12756E7CF098064 (liga_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chat_liga ADD CONSTRAINT FK_A12756E7DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE chat_liga ADD CONSTRAINT FK_A12756E7CF098064 FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('ALTER TABLE alineacion DROP FOREIGN KEY `FK_A944AAC126E992D9`');
        $this->addSql('ALTER TABLE alineacion DROP FOREIGN KEY `FK_A944AAC1A8F03061`');
        $this->addSql('ALTER TABLE eleccion_estrella DROP FOREIGN KEY `FK_1E0BA57811856EB4`');
        $this->addSql('ALTER TABLE eleccion_estrella DROP FOREIGN KEY `FK_1E0BA578B8A54D43`');
        $this->addSql('ALTER TABLE eleccion_estrella DROP FOREIGN KEY `FK_1E0BA578D375E804`');
        $this->addSql('DROP TABLE alineacion');
        $this->addSql('DROP TABLE eleccion_estrella');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alineacion (id INT AUTO_INCREMENT NOT NULL, puntos_jornada INT NOT NULL, usuario_fantasy_id INT DEFAULT NULL, jornada_id INT DEFAULT NULL, INDEX IDX_A944AAC126E992D9 (jornada_id), INDEX IDX_A944AAC1A8F03061 (usuario_fantasy_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE eleccion_estrella (id INT AUTO_INCREMENT NOT NULL, alineacion_id INT NOT NULL, partido_id INT DEFAULT NULL, jugador_id INT DEFAULT NULL, INDEX IDX_1E0BA57811856EB4 (partido_id), UNIQUE INDEX unique_partido_alineacion (alineacion_id, partido_id), INDEX IDX_1E0BA578B8A54D43 (jugador_id), INDEX IDX_1E0BA578D375E804 (alineacion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE alineacion ADD CONSTRAINT `FK_A944AAC126E992D9` FOREIGN KEY (jornada_id) REFERENCES jornada (id)');
        $this->addSql('ALTER TABLE alineacion ADD CONSTRAINT `FK_A944AAC1A8F03061` FOREIGN KEY (usuario_fantasy_id) REFERENCES usuario_fantasy (id)');
        $this->addSql('ALTER TABLE eleccion_estrella ADD CONSTRAINT `FK_1E0BA57811856EB4` FOREIGN KEY (partido_id) REFERENCES partido (id)');
        $this->addSql('ALTER TABLE eleccion_estrella ADD CONSTRAINT `FK_1E0BA578B8A54D43` FOREIGN KEY (jugador_id) REFERENCES jugador (id)');
        $this->addSql('ALTER TABLE eleccion_estrella ADD CONSTRAINT `FK_1E0BA578D375E804` FOREIGN KEY (alineacion_id) REFERENCES alineacion (id)');
        $this->addSql('ALTER TABLE chat_liga DROP FOREIGN KEY FK_A12756E7DB38439E');
        $this->addSql('ALTER TABLE chat_liga DROP FOREIGN KEY FK_A12756E7CF098064');
        $this->addSql('DROP TABLE chat_liga');
    }
}
