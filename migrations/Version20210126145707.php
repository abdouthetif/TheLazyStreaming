<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126145707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liste DROP FOREIGN KEY FK_FCF22AF479F37AE5');
        $this->addSql('DROP INDEX IDX_FCF22AF479F37AE5 ON liste');
        $this->addSql('ALTER TABLE liste CHANGE id_user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE liste ADD CONSTRAINT FK_FCF22AF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FCF22AF4A76ED395 ON liste (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liste DROP FOREIGN KEY FK_FCF22AF4A76ED395');
        $this->addSql('DROP INDEX IDX_FCF22AF4A76ED395 ON liste');
        $this->addSql('ALTER TABLE liste CHANGE user_id id_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE liste ADD CONSTRAINT FK_FCF22AF479F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FCF22AF479F37AE5 ON liste (id_user_id)');
    }
}
