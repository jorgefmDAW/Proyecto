<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260601113749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario ADD liga_seleccionada_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DAD3EF5BD FOREIGN KEY (liga_seleccionada_id) REFERENCES liga (id)');
        $this->addSql('CREATE INDEX IDX_2265B05DAD3EF5BD ON usuario (liga_seleccionada_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DAD3EF5BD');
        $this->addSql('DROP INDEX IDX_2265B05DAD3EF5BD ON usuario');
        $this->addSql('ALTER TABLE usuario DROP liga_seleccionada_id');
    }
}
