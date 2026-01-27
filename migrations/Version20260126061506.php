<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126061506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bot_orders (id SERIAL NOT NULL, user_id INT NOT NULL, username VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, task TEXT DEFAULT NULL, phone VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bot_orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "user" ALTER last_name DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE bot_orders');
        $this->addSql('ALTER TABLE "user" ALTER last_name SET NOT NULL');
    }
}
