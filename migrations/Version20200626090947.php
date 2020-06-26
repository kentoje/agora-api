<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200626090947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE date (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, level_number INT NOT NULL, reduction_rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesure (id INT AUTO_INCREMENT NOT NULL, to_mesure_id INT DEFAULT NULL, date_id INT DEFAULT NULL, water DOUBLE PRECISION NOT NULL, electricity DOUBLE PRECISION NOT NULL, gas DOUBLE PRECISION NOT NULL, waste DOUBLE PRECISION NOT NULL, navigo_subscription TINYINT(1) NOT NULL, INDEX IDX_5F1B6E707208F58B (to_mesure_id), INDEX IDX_5F1B6E70B897366B (date_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, date_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, unit VARCHAR(50) NOT NULL, validate TINYINT(1) NOT NULL, INDEX IDX_527EDB25B897366B (date_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_user (task_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FE2042328DB60186 (task_id), INDEX IDX_FE204232A76ED395 (user_id), PRIMARY KEY(task_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, level_id INT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, image VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, agora_number INT NOT NULL, nb_resident INT NOT NULL, living_area DOUBLE PRECISION NOT NULL, gas TINYINT(1) NOT NULL, insulation TINYINT(1) NOT NULL, nif_number VARCHAR(30) NOT NULL, gas_average_consumption DOUBLE PRECISION NOT NULL, water_average_consumption DOUBLE PRECISION NOT NULL, electricity_average_consumption DOUBLE PRECISION NOT NULL, waste_average_consumption DOUBLE PRECISION NOT NULL, registration_date DATE NOT NULL, navigo_number INT DEFAULT NULL, saving_water DOUBLE PRECISION NOT NULL, saving_electricity DOUBLE PRECISION NOT NULL, saving_gas DOUBLE PRECISION NOT NULL, saving_waste DOUBLE PRECISION NOT NULL, INDEX IDX_8D93D6495FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E707208F58B FOREIGN KEY (to_mesure_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70B897366B FOREIGN KEY (date_id) REFERENCES date (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25B897366B FOREIGN KEY (date_id) REFERENCES date (id)');
        $this->addSql('ALTER TABLE task_user ADD CONSTRAINT FK_FE2042328DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_user ADD CONSTRAINT FK_FE204232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70B897366B');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25B897366B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495FB14BA7');
        $this->addSql('ALTER TABLE task_user DROP FOREIGN KEY FK_FE2042328DB60186');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E707208F58B');
        $this->addSql('ALTER TABLE task_user DROP FOREIGN KEY FK_FE204232A76ED395');
        $this->addSql('DROP TABLE date');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE mesure');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_user');
        $this->addSql('DROP TABLE user');
    }
}
