<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029184327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add in_reply_to relationship to Message entity to link bot responses to original messages';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD in_reply_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FDD92DAB8 FOREIGN KEY (in_reply_to_id) REFERENCES message (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_B6BD307FDD92DAB8 ON message (in_reply_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FDD92DAB8');
        $this->addSql('DROP INDEX IDX_B6BD307FDD92DAB8');
        $this->addSql('ALTER TABLE message DROP in_reply_to_id');
    }
}
