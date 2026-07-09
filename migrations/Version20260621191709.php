<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260621191709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table de jointure category_supplier (relation ManyToMany entre catégories et fournisseurs) avec suppression en cascade.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE category_supplier (category_id INT NOT NULL, supplier_id INT NOT NULL, PRIMARY KEY (category_id, supplier_id))');
        $this->addSql('CREATE INDEX IDX_2C50E80512469DE2 ON category_supplier (category_id)');
        $this->addSql('CREATE INDEX IDX_2C50E8052ADD6D8C ON category_supplier (supplier_id)');
        $this->addSql('ALTER TABLE category_supplier ADD CONSTRAINT FK_2C50E80512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_supplier ADD CONSTRAINT FK_2C50E8052ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_supplier DROP CONSTRAINT FK_2C50E80512469DE2');
        $this->addSql('ALTER TABLE category_supplier DROP CONSTRAINT FK_2C50E8052ADD6D8C');
        $this->addSql('DROP TABLE category_supplier');
    }
}
