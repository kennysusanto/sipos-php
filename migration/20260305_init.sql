CREATE DATABASE IF NOT EXISTS `testing-github-copilot` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `testing-github-copilot`;

CREATE TABLE IF NOT EXISTS `tenant` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `display_name` VARCHAR(150) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL
);

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_user_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant`(`id`)
);

INSERT INTO `testing-github-copilot`.`tenant` (`name`, `display_name`, `created_at`, `updated_at`, `deleted_at`)
VALUES ('master', 'Master Tenant', NOW(), NULL, NULL)
ON DUPLICATE KEY UPDATE
  `display_name` = VALUES(`display_name`),
  `deleted_at` = NULL;

INSERT INTO `testing-github-copilot`.`user` (`tenant_id`, `username`, `email`, `role`, `password`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`, 'admin', 'admin@example.com', 'admin', 'password', NOW(), NULL, NULL
FROM `testing-github-copilot`.`tenant`
WHERE `name` = 'master'
ON DUPLICATE KEY UPDATE
  `tenant_id` = VALUES(`tenant_id`),
  `email` = VALUES(`email`),
  `role` = VALUES(`role`),
  `password` = VALUES(`password`),
  `deleted_at` = NULL;

INSERT INTO `testing-github-copilot`.`user` (`tenant_id`, `username`, `email`, `role`, `password`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`, 'kenny', 'kenny@example.com', 'user', 'password', NOW(), NULL, NULL
FROM `testing-github-copilot`.`tenant`
WHERE `name` = 'master'
ON DUPLICATE KEY UPDATE
  `tenant_id` = VALUES(`tenant_id`),
  `email` = VALUES(`email`),
  `role` = VALUES(`role`),
  `password` = VALUES(`password`),
  `deleted_at` = NULL;

CREATE TABLE IF NOT EXISTS `menuitem` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT NOT NULL,
  `display_name` VARCHAR(150) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `url` VARCHAR(500) NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_menuitem_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant`(`id`),
  UNIQUE KEY `uniq_menuitem_tenant_name` (`tenant_id`, `name`)
);

CREATE TABLE IF NOT EXISTS `bill` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_id` INT NULL,
  `note` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL
);

CREATE TABLE IF NOT EXISTS `billitem` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bill_id` INT NOT NULL,
  `menuitem_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_billitem_bill` FOREIGN KEY (`bill_id`) REFERENCES `bill`(`id`),
  CONSTRAINT `fk_billitem_menuitem` FOREIGN KEY (`menuitem_id`) REFERENCES `menuitem`(`id`)
);
