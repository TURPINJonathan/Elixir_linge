<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222015944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Split promo kind and discount mode; convert amount to decimal; add currency';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promo_code ADD discount_type VARCHAR(255) NOT NULL, ADD currency VARCHAR(3) NOT NULL, CHANGE amount amount NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql("UPDATE promo_code SET discount_type = 'fixed' WHERE type = 'fixed'");
        $this->addSql("UPDATE promo_code SET discount_type = 'percentage' WHERE type = 'percentage'");
        $this->addSql("UPDATE promo_code SET discount_type = 'percentage' WHERE discount_type NOT IN ('percentage', 'fixed')");
        $this->addSql("UPDATE promo_code SET type = 'custom' WHERE type IN ('fixed', 'percentage')");
        $this->addSql("UPDATE promo_code SET type = 'custom' WHERE type NOT IN ('custom', 'general', 'service')");
        $this->addSql("UPDATE promo_code SET currency = 'EUR' WHERE currency IS NULL OR currency = ''");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promo_code DROP discount_type, DROP currency, CHANGE amount amount VARCHAR(10) DEFAULT NULL');
    }
}
