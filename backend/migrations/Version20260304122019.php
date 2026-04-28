<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304122019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY `FK_4E79750B7EB879ED`');
        $this->addSql('DROP INDEX IDX_4E79750B7EB879ED ON partido');
        $this->addSql('ALTER TABLE partido DROP jornada, CHANGE jornada_id_id jornada_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750B26E992D9 FOREIGN KEY (jornada_id) REFERENCES jornada (id)');
        $this->addSql('CREATE INDEX IDX_4E79750B26E992D9 ON partido (jornada_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750B26E992D9');
        $this->addSql('DROP INDEX IDX_4E79750B26E992D9 ON partido');
        $this->addSql('ALTER TABLE partido ADD jornada INT NOT NULL, CHANGE jornada_id jornada_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT `FK_4E79750B7EB879ED` FOREIGN KEY (jornada_id_id) REFERENCES jornada (id)');
        $this->addSql('CREATE INDEX IDX_4E79750B7EB879ED ON partido (jornada_id_id)');
    }
}
