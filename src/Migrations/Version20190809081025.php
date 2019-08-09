<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190809081025 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE currents (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, type_id INT NOT NULL, recuring_id INT DEFAULT NULL, name VARCHAR(45) NOT NULL, date DATE NOT NULL, value DOUBLE PRECISION NOT NULL, checked TINYINT(1) NOT NULL, INDEX IDX_8C00E5B712469DE2 (category_id), INDEX IDX_8C00E5B7C54C8C93 (type_id), INDEX IDX_8C00E5B736A6325B (recuring_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE currents ADD CONSTRAINT FK_8C00E5B712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE currents ADD CONSTRAINT FK_8C00E5B7C54C8C93 FOREIGN KEY (type_id) REFERENCES types (id)');
        $this->addSql('ALTER TABLE currents ADD CONSTRAINT FK_8C00E5B736A6325B FOREIGN KEY (recuring_id) REFERENCES recuring (id)');
        $this->addSql('ALTER TABLE recuring RENAME INDEX idx_431312e78eb23357 TO IDX_431312E7C54C8C93');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE currents');
        $this->addSql('ALTER TABLE recuring RENAME INDEX idx_431312e7c54c8c93 TO IDX_431312E78EB23357');
    }
}
