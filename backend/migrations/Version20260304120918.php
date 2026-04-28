<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304120918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY `FK_4E79750B326E004A`');
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY `FK_4E79750B60A20253`');
        $this->addSql('DROP INDEX IDX_4E79750B60A20253 ON partido');
        $this->addSql('DROP INDEX IDX_4E79750B326E004A ON partido');
        $this->addSql('ALTER TABLE partido ADD local_id INT NOT NULL, ADD visitante_id INT NOT NULL, DROP local_id_id, DROP visitante_id_id');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750B5D5A2101 FOREIGN KEY (local_id) REFERENCES equipo (id)');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT FK_4E79750BD80AA8AF FOREIGN KEY (visitante_id) REFERENCES equipo (id)');
        $this->addSql('CREATE INDEX IDX_4E79750B5D5A2101 ON partido (local_id)');
        $this->addSql('CREATE INDEX IDX_4E79750BD80AA8AF ON partido (visitante_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750B5D5A2101');
        $this->addSql('ALTER TABLE partido DROP FOREIGN KEY FK_4E79750BD80AA8AF');
        $this->addSql('DROP INDEX IDX_4E79750B5D5A2101 ON partido');
        $this->addSql('DROP INDEX IDX_4E79750BD80AA8AF ON partido');
        $this->addSql('ALTER TABLE partido ADD local_id_id INT NOT NULL, ADD visitante_id_id INT NOT NULL, DROP local_id, DROP visitante_id');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT `FK_4E79750B326E004A` FOREIGN KEY (visitante_id_id) REFERENCES equipo (id)');
        $this->addSql('ALTER TABLE partido ADD CONSTRAINT `FK_4E79750B60A20253` FOREIGN KEY (local_id_id) REFERENCES equipo (id)');
        $this->addSql('CREATE INDEX IDX_4E79750B60A20253 ON partido (local_id_id)');
        $this->addSql('CREATE INDEX IDX_4E79750B326E004A ON partido (visitante_id_id)');
    }
}
