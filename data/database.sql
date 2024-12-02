CREATE DATABASE socialchat;

USE socialchat;

CREATE TABLE post(
    id int PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    comment VARCHAR(200) NOT NULL
);