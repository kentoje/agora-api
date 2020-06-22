<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200622123626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E7043AB22FA');
        $this->addSql('DROP INDEX IDX_5F1B6E7043AB22FA ON mesure');
        $this->addSql('ALTER TABLE mesure CHANGE mesure_id to_mesure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E707208F58B FOREIGN KEY (to_mesure_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5F1B6E707208F58B ON mesure (to_mesure_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E707208F58B');
        $this->addSql('DROP INDEX IDX_5F1B6E707208F58B ON mesure');
        $this->addSql('ALTER TABLE mesure CHANGE to_mesure_id mesure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E7043AB22FA FOREIGN KEY (mesure_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5F1B6E7043AB22FA ON mesure (mesure_id)');
    }
}
