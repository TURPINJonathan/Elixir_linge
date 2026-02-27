<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table media_file (médiathèque avec compression)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE media_file (
                id              INT AUTO_INCREMENT NOT NULL,
                original_name   VARCHAR(255) NOT NULL,
                mime_type       VARCHAR(100) NOT NULL,
                size            INT NOT NULL,
                stored_size     INT NOT NULL,
                alt             VARCHAR(255) DEFAULT NULL,
                stored_filename VARCHAR(64) NOT NULL,
                is_compressed   TINYINT(1) NOT NULL,
                has_thumbnail   TINYINT(1) NOT NULL,
                created_at      DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at      DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE media_file');
    }
}
