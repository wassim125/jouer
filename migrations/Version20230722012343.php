<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230722012343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clientproduct_product DROP FOREIGN KEY FK_FF29001DE3381748');
        $this->addSql('ALTER TABLE clientproduct_product DROP FOREIGN KEY FK_FF29001D4584665A');
        $this->addSql('DROP TABLE clientproduct_product');
        $this->addSql('ALTER TABLE clientproduct ADD numero_telephone INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clientproduct_product (clientproduct_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_FF29001DE3381748 (clientproduct_id), INDEX IDX_FF29001D4584665A (product_id), PRIMARY KEY(clientproduct_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE clientproduct_product ADD CONSTRAINT FK_FF29001DE3381748 FOREIGN KEY (clientproduct_id) REFERENCES clientproduct (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clientproduct_product ADD CONSTRAINT FK_FF29001D4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clientproduct DROP numero_telephone');
    }
}
