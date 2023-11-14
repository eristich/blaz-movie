<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114163151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE movie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE person_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rel_actor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rel_director_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE movie (id INT NOT NULL, name VARCHAR(128) NOT NULL, description TEXT DEFAULT NULL, publication_on DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE person (id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, birthday DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE rel_actor (id INT NOT NULL, person_id INT NOT NULL, movie_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_93CE6C75217BBB47 ON rel_actor (person_id)');
        $this->addSql('CREATE INDEX IDX_93CE6C758F93B6FC ON rel_actor (movie_id)');
        $this->addSql('CREATE UNIQUE INDEX actor_composite_idx ON rel_actor (person_id, movie_id)');
        $this->addSql('CREATE TABLE rel_director (id INT NOT NULL, person_id INT NOT NULL, movie_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FA585BE217BBB47 ON rel_director (person_id)');
        $this->addSql('CREATE INDEX IDX_8FA585BE8F93B6FC ON rel_director (movie_id)');
        $this->addSql('CREATE UNIQUE INDEX director_composite_idx ON rel_director (person_id, movie_id)');
        $this->addSql('ALTER TABLE rel_actor ADD CONSTRAINT FK_93CE6C75217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rel_actor ADD CONSTRAINT FK_93CE6C758F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rel_director ADD CONSTRAINT FK_8FA585BE217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rel_director ADD CONSTRAINT FK_8FA585BE8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE movie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE person_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rel_actor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rel_director_id_seq CASCADE');
        $this->addSql('ALTER TABLE rel_actor DROP CONSTRAINT FK_93CE6C75217BBB47');
        $this->addSql('ALTER TABLE rel_actor DROP CONSTRAINT FK_93CE6C758F93B6FC');
        $this->addSql('ALTER TABLE rel_director DROP CONSTRAINT FK_8FA585BE217BBB47');
        $this->addSql('ALTER TABLE rel_director DROP CONSTRAINT FK_8FA585BE8F93B6FC');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE rel_actor');
        $this->addSql('DROP TABLE rel_director');
    }
}
