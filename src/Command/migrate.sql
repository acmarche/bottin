RENAME TABLE `bottin`.`categories` TO `bottin`.`category`;
RENAME TABLE `bottin`.`fiches` TO `bottin`.`fiche`;
ALTER TABLE `fiche` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `fiche` DROP `created_at`, DROP `updated_at`;
ALTER TABLE `fiche` CHANGE `createdAt` `created_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `fiche` CHANGE `updatedAt` `updated_at` DATETIME NULL DEFAULT NULL;
UPDATE fiche SET fiche.created = '2020-03-26 12:00:00' WHERE CAST(fiche.created AS CHAR(20)) = '0000-00-00 00:00:00';
UPDATE fiche SET fiche.created_at = '2020-03-26 12:00:00' WHERE CAST(fiche.created_at AS CHAR(20)) = '0000-00-00 00:00:00';
UPDATE fiche SET fiche.updated = '2020-03-26 12:00:00' WHERE CAST(fiche.updated AS CHAR(20)) = '0000-00-00 00:00:00';
UPDATE fiche SET fiche.updated_at = '2020-03-26 12:00:00' WHERE CAST(fiche.updated_at AS CHAR(20)) = '0000-00-00 00:00:00';
ALTER TABLE `fiche` CHANGE `slugname` `slug` VARCHAR(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `category` DROP `created_at`, DROP `updated_at`;
ALTER TABLE `category` CHANGE `createdAt` `created_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `category` CHANGE `updatedAt` `updated_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE `category` CHANGE `slugname` `slug` VARCHAR(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `demande` CHANGE `created` `created_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `demande` CHANGE `updated` `updated_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `pdv` CHANGE `slugname` `slug` VARCHAR(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

TRUNCATE TABLE `user`;
DROP TABLE `demandes`;
DROP TABLE `destinataires`;
DROP TABLE `destinataires_old`;
DROP TABLE `exclut`;
DROP TABLE `news`;
DROP TABLE `newsletter`;
