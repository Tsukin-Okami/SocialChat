DROP DATABASE IF EXISTS `socialchat`;
CREATE DATABASE `socialchat`;

USE `socialchat`;

CREATE TABLE `user`(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `bio` VARCHAR(200)
);

CREATE TABLE `post`(
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(50) NOT NULL,
    `comment` VARCHAR(200) NOT NULL,
    `owner` INT NOT NULL,
    FOREIGN KEY (`owner`) REFERENCES `user`(`id`) ON DELETE CASCADE
);