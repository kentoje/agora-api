<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200622120027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, agora_number INT NOT NULL, nb_resident INT NOT NULL, living_area DOUBLE PRECISION NOT NULL, gas TINYINT(1) NOT NULL, insulation TINYINT(1) NOT NULL, social_security_number VARCHAR(15) NOT NULL, gas_average_consumption DOUBLE PRECISION NOT NULL, water_average_consumption DOUBLE PRECISION NOT NULL, electricity_average_consumption DOUBLE PRECISION NOT NULL, waste_average_consumption DOUBLE PRECISION NOT NULL, registration_date DATE NOT NULL, navigo_number INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
