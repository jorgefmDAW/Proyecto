<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304121912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jornada (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE partido ADD jornada_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750B7EB879ED FOREIGN KEY (jornada_id_id) REFERENCES jornada (id)');
        $this->addSql('CREATE INDEX IDX_4E79750B7EB879ED ON partido (jornada_id_id)');
        $this->addSql('ALTER TABLE puntuacion ADD jornada_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE puntuacion ADD CONSTRAINT FK_ABF67C3F7EB879ED FOREIGN KEY (jornada_id_id) REFERENCES jornada (id)');
        $this->addSql('CREATE INDEX IDX_ABF67C3F7EB879ED ON puntuacion (jornada_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE jornada');
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750B7EB879ED');
        $this->addSql('DROP INDEX IDX_4E79750B7EB879ED ON partido');
        $this->addSql('ALTER TABLE partido DROP jornada_id_id');
        $this->addSql('ALTER TABLE puntuacion DROP FOREIGN KEY FK_ABF67C3F7EB879ED');
        $this->addSql('DROP INDEX IDX_ABF67C3F7EB879ED ON puntuacion');
        $this->addSql('ALTER TABLE puntuacion DROP jornada_id_id');
    }
}
