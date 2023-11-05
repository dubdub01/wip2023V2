<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231104115631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_worker (user_id INT NOT NULL, worker_id INT NOT NULL, INDEX IDX_FAE0A45FA76ED395 (user_id), INDEX IDX_FAE0A45F6B20BA36 (worker_id), PRIMARY KEY(user_id, worker_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_worker ADD CONSTRAINT FK_FAE0A45FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_worker ADD CONSTRAINT FK_FAE0A45F6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE has_contacted_user DROP FOREIGN KEY FK_36741CE2A76ED395');
        $this->addSql('ALTER TABLE has_contacted_user DROP FOREIGN KEY FK_36741CE25163F491');
        $this->addSql('ALTER TABLE has_contacted_worker DROP FOREIGN KEY FK_A9F04A095163F491');
        $this->addSql('ALTER TABLE has_contacted_worker DROP FOREIGN KEY FK_A9F04A096B20BA36');
        $this->addSql('DROP TABLE has_contacted');
        $this->addSql('DROP TABLE has_contacted_user');
        $this->addSql('DROP TABLE has_contacted_worker');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE has_contacted (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE has_contacted_user (has_contacted_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_36741CE2A76ED395 (user_id), INDEX IDX_36741CE25163F491 (has_contacted_id), PRIMARY KEY(has_contacted_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE has_contacted_worker (has_contacted_id INT NOT NULL, worker_id INT NOT NULL, INDEX IDX_A9F04A096B20BA36 (worker_id), INDEX IDX_A9F04A095163F491 (has_contacted_id), PRIMARY KEY(has_contacted_id, worker_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE has_contacted_user ADD CONSTRAINT FK_36741CE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE has_contacted_user ADD CONSTRAINT FK_36741CE25163F491 FOREIGN KEY (has_contacted_id) REFERENCES has_contacted (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE has_contacted_worker ADD CONSTRAINT FK_A9F04A095163F491 FOREIGN KEY (has_contacted_id) REFERENCES has_contacted (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE has_contacted_worker ADD CONSTRAINT FK_A9F04A096B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_worker DROP FOREIGN KEY FK_FAE0A45FA76ED395');
        $this->addSql('ALTER TABLE user_worker DROP FOREIGN KEY FK_FAE0A45F6B20BA36');
        $this->addSql('DROP TABLE user_worker');
    }
}
