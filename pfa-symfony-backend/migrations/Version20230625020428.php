<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625020428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE crop (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, crop_name VARCHAR(255) NOT NULL, fad VARCHAR(255) NOT NULL, max_root_depth VARCHAR(255) NOT NULL, harvested_green TINYINT(1) NOT NULL, stages LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', sow_depth VARCHAR(255) NOT NULL, INDEX IDX_EDC23D9B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE farm (id INT AUTO_INCREMENT NOT NULL, farmer_id INT NOT NULL, weather_station_id INT DEFAULT NULL, farm_name VARCHAR(50) NOT NULL, area VARCHAR(255) NOT NULL, INDEX IDX_5816D04513481D2B (farmer_id), INDEX IDX_5816D0459E475DA2 (weather_station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE farmer (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, country_code VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_EC85AC8FE7927C74 (email), UNIQUE INDEX UNIQ_EC85AC8F444F97DD (phone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE otp_codes (id INT AUTO_INCREMENT NOT NULL, phone_number VARCHAR(50) NOT NULL, code VARCHAR(10) NOT NULL, expiration_date DATETIME NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE output (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, crop_id INT DEFAULT NULL, soil_id INT DEFAULT NULL, date_of_calculations DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', t_max DOUBLE PRECISION DEFAULT NULL, t_min DOUBLE PRECISION DEFAULT NULL, r_hmax DOUBLE PRECISION DEFAULT NULL, r_hmin DOUBLE PRECISION DEFAULT NULL, wind_speed DOUBLE PRECISION DEFAULT NULL, s_rad DOUBLE PRECISION DEFAULT NULL, precipitations DOUBLE PRECISION DEFAULT NULL, et0 DOUBLE PRECISION DEFAULT NULL, kc DOUBLE PRECISION DEFAULT NULL, z_root DOUBLE PRECISION DEFAULT NULL, z_root_real DOUBLE PRECISION DEFAULT NULL, swd DOUBLE PRECISION DEFAULT NULL, swdc DOUBLE PRECISION DEFAULT NULL, irr DOUBLE PRECISION DEFAULT NULL, etc DOUBLE PRECISION DEFAULT NULL, das INT DEFAULT 1, INDEX IDX_CCDE149E7E3C61F9 (owner_id), INDEX IDX_CCDE149E888579EE (crop_id), INDEX IDX_CCDE149EC59CE9E2 (soil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcel (id INT AUTO_INCREMENT NOT NULL, farm_id INT NOT NULL, soil_id INT DEFAULT NULL, crop_id INT DEFAULT NULL, parcel_name VARCHAR(255) NOT NULL, area VARCHAR(255) NOT NULL, INDEX IDX_C99B5D6065FCFA0D (farm_id), INDEX IDX_C99B5D60C59CE9E2 (soil_id), INDEX IDX_C99B5D60888579EE (crop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE probe (id INT AUTO_INCREMENT NOT NULL, parcel_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_D75E6F2A465E670C (parcel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_C74F2195C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soil (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, paw VARCHAR(255) NOT NULL, depth VARCHAR(255) NOT NULL, INDEX IDX_EB7EA1EE7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weather_station (id INT AUTO_INCREMENT NOT NULL, station_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crop ADD CONSTRAINT FK_EDC23D9B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES farmer (id)');
        $this->addSql('ALTER TABLE farm ADD CONSTRAINT FK_5816D04513481D2B FOREIGN KEY (farmer_id) REFERENCES farmer (id)');
        $this->addSql('ALTER TABLE farm ADD CONSTRAINT FK_5816D0459E475DA2 FOREIGN KEY (weather_station_id) REFERENCES weather_station (id)');
        $this->addSql('ALTER TABLE output ADD CONSTRAINT FK_CCDE149E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES farmer (id)');
        $this->addSql('ALTER TABLE output ADD CONSTRAINT FK_CCDE149E888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE output ADD CONSTRAINT FK_CCDE149EC59CE9E2 FOREIGN KEY (soil_id) REFERENCES soil (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D6065FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60C59CE9E2 FOREIGN KEY (soil_id) REFERENCES soil (id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60888579EE FOREIGN KEY (crop_id) REFERENCES crop (id)');
        $this->addSql('ALTER TABLE probe ADD CONSTRAINT FK_D75E6F2A465E670C FOREIGN KEY (parcel_id) REFERENCES parcel (id)');
        $this->addSql('ALTER TABLE soil ADD CONSTRAINT FK_EB7EA1EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES farmer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crop DROP FOREIGN KEY FK_EDC23D9B7E3C61F9');
        $this->addSql('ALTER TABLE farm DROP FOREIGN KEY FK_5816D04513481D2B');
        $this->addSql('ALTER TABLE farm DROP FOREIGN KEY FK_5816D0459E475DA2');
        $this->addSql('ALTER TABLE output DROP FOREIGN KEY FK_CCDE149E7E3C61F9');
        $this->addSql('ALTER TABLE output DROP FOREIGN KEY FK_CCDE149E888579EE');
        $this->addSql('ALTER TABLE output DROP FOREIGN KEY FK_CCDE149EC59CE9E2');
        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D6065FCFA0D');
        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D60C59CE9E2');
        $this->addSql('ALTER TABLE parcel DROP FOREIGN KEY FK_C99B5D60888579EE');
        $this->addSql('ALTER TABLE probe DROP FOREIGN KEY FK_D75E6F2A465E670C');
        $this->addSql('ALTER TABLE soil DROP FOREIGN KEY FK_EB7EA1EE7E3C61F9');
        $this->addSql('DROP TABLE crop');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP TABLE farmer');
        $this->addSql('DROP TABLE otp_codes');
        $this->addSql('DROP TABLE output');
        $this->addSql('DROP TABLE parcel');
        $this->addSql('DROP TABLE probe');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE soil');
        $this->addSql('DROP TABLE weather_station');
    }
}
