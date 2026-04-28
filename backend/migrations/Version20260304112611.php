<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304112611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partido ADD local_id INT NOT NULL, ADD visitante_id INT NOT NULL, DROP local, DROP visitante');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partido ADD local VARCHAR(255) NOT NULL, ADD visitante VARCHAR(255) NOT NULL, DROP local_id, DROP visitante_id');
    }
}
