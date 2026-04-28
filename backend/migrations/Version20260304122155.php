<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304122155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puntuacion DROP FOREIGN KEY `FK_ABF67C3F7EB879ED`');
        $this->addSql('DROP INDEX IDX_ABF67C3F7EB879ED ON puntuacion');
        $this->addSql('ALTER TABLE puntuacion DROP jornada, CHANGE jornada_id_id jornada_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE puntuacion ADD CONSTRAINT FK_ABF67C3F26E992D9 FOREIGN KEY (jornada_id) REFERENCES jornada (id)');
        $this->addSql('CREATE INDEX IDX_ABF67C3F26E992D9 ON puntuacion (jornada_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puntuacion DROP FOREIGN KEY FK_ABF67C3F26E992D9');
        $this->addSql('DROP INDEX IDX_ABF67C3F26E992D9 ON puntuacion');
        $this->addSql('ALTER TABLE puntuacion ADD jornada INT NOT NULL, CHANGE jornada_id jornada_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE puntuacion ADD CONSTRAINT `FK_ABF67C3F7EB879ED` FOREIGN KEY (jornada_id_id) REFERENCES jornada (id)');
        $this->addSql('CREATE INDEX IDX_ABF67C3F7EB879ED ON puntuacion (jornada_id_id)');
    }
}
