CREATE DATABASE dolphin_crm;
USE dolphin_crm;

CREATE TABLE `dolphin_crm`.`users` (
    `id` INT NOT NULL AUTO_INCREMENT, 
    `firstname` VARCHAR(35) NOT NULL, 
    `lastname` VARCHAR(35) NOT NULL, 
    `password` VARCHAR(255) NOT NULL, 
    `email` VARCHAR(100) NOT NULL, 
    `role` VARCHAR(60) NOT NULL, 
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dolphin_crm`.`contact` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `title` VARCHAR(20) NOT NULL,
    `firstname` VARCHAR(35) NOT NULL,
    `lastname` VARCHAR(35) NOT NULL ,
    `email` VARCHAR(100) NOT NULL,
    `telephone` VARCHAR(15) NOT NULL,
    `company` VARCHAR(100) NOT NULL,
    `type` VARCHAR(11) NOT NULL,
    `assigned_to` INT NOT NULL, 
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dolphin_crm`.`notes` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT NOT NULL,
    `comment` TEXT NOT NULL,
    `created_by` INT NOT NULL ,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contact`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`firstname`, `lastname`, `password`, `email`, `role`) 
VALUES ('John', 'Doe', '$2y$10$ddIpI4n.X.BnDJJeK1bhY.heVPZZx7TG.sPkCrdjdyGXG2bu7snb6', 'admin@project2.com', 'admin');