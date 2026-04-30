<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430080715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alineacion (id INT AUTO_INCREMENT NOT NULL, puntos_jornada INT NOT NULL, usuario_fantasy_id INT DEFAULT NULL, jornada_id INT DEFAULT NULL, INDEX IDX_A944AAC1A8F03061 (usuario_fantasy_id), INDEX IDX_A944AAC126E992D9 (jornada_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE eleccion_estrella (id INT AUTO_INCREMENT NOT NULL, alineacion_id INT DEFAULT NULL, partido_id INT DEFAULT NULL, jugador_id INT DEFAULT NULL, INDEX IDX_1E0BA578D375E804 (alineacion_id), INDEX IDX_1E0BA57811856EB4 (partido_id), INDEX IDX_1E0BA578B8A54D43 (jugador_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuario_fantasy (id INT AUTO_INCREMENT NOT NULL, puntos_totales INT NOT NULL, usuario_id INT DEFAULT NULL, liga_id INT DEFAULT NULL, INDEX IDX_69056C44DB38439E (usuario_id), INDEX IDX_69056C44CF098064 (liga_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE alineacion ADD CONSTRAINT FK_A944AAC1A8F03061 FOREIGN KEY (usuario_fantasy_id) REFERENCES usuario_fantasy (id)');
        $this->addSql('ALTER TABLE alineacion ADD CONSTRAINT FK_A944AAC126E992D9 FOREIGN KEY (jornada_id) REFERENCES jornada (id)');
        $this->addSql('ALTER TABLE eleccion_estrella ADD CONSTRAINT FK_1E0BA578D375E804 FOREIGN KEY (alineacion_id) REFERENCES alineacion (id)');
        $this->addSql('ALTER TABLE eleccion_estrella ADD CONSTRAINT FK_1E0BA57811856EB4 FOREIGN KEY (partido_id) REFERENCES partido (id)');
        $this->addSql('ALTER TABLE eleccion_estrella ADD CONSTRAINT FK_1E0BA578B8A54D43 FOREIGN KEY (jugador_id) REFERENCES jugador (id)');
        $this->addSql('ALTER TABLE usuario_fantasy ADD CONSTRAINT FK_69056C44DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario_fantasy ADD CONSTRAINT FK_69056C44CF098064 FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY `FK_2265B05DCF098064`');
        $this->addSql('DROP INDEX IDX_2265B05DCF098064 ON usuario');
        $this->addSql('ALTER TABLE usuario DROP liga_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alineacion DROP FOREIGN KEY FK_A944AAC1A8F03061');
        $this->addSql('ALTER TABLE alineacion DROP FOREIGN KEY FK_A944AAC126E992D9');
        $this->addSql('ALTER TABLE eleccion_estrella DROP FOREIGN KEY FK_1E0BA578D375E804');
        $this->addSql('ALTER TABLE eleccion_estrella DROP FOREIGN KEY FK_1E0BA57811856EB4');
        $this->addSql('ALTER TABLE eleccion_estrella DROP FOREIGN KEY FK_1E0BA578B8A54D43');
        $this->addSql('ALTER TABLE usuario_fantasy DROP FOREIGN KEY FK_69056C44DB38439E');
        $this->addSql('ALTER TABLE usuario_fantasy DROP FOREIGN KEY FK_69056C44CF098064');
        $this->addSql('DROP TABLE alineacion');
        $this->addSql('DROP TABLE eleccion_estrella');
        $this->addSql('DROP TABLE usuario_fantasy');
        $this->addSql('ALTER TABLE usuario ADD liga_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT `FK_2265B05DCF098064` FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('CREATE INDEX IDX_2265B05DCF098064 ON usuario (liga_id)');
    }
}
