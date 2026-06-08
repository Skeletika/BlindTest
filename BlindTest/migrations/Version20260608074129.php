<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260608074129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5FCCE328B FOREIGN KEY (clue_id) REFERENCES clues (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5AA334807 FOREIGN KEY (answer_id) REFERENCES answers (id)');
        $this->addSql('CREATE INDEX IDX_8ADC54D5BCF5E72D ON questions (categorie_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8ADC54D5FCCE328B ON questions (clue_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8ADC54D5AA334807 ON questions (answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5BCF5E72D');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5FCCE328B');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5AA334807');
        $this->addSql('DROP INDEX IDX_8ADC54D5BCF5E72D ON questions');
        $this->addSql('DROP INDEX UNIQ_8ADC54D5FCCE328B ON questions');
        $this->addSql('DROP INDEX UNIQ_8ADC54D5AA334807 ON questions');
    }
}
