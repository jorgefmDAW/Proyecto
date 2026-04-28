<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309133707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario ADD liga_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DCF098064 FOREIGN KEY (liga_id) REFERENCES liga (id)');
        $this->addSql('CREATE INDEX IDX_2265B05DCF098064 ON usuario (liga_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DCF098064');
        $this->addSql('DROP INDEX IDX_2265B05DCF098064 ON usuario');
        $this->addSql('ALTER TABLE usuario DROP liga_id');
    }
}
