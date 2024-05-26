CREATE DATABASE IF NOT EXISTS testdb;

USE testdb;

CREATE TABLE IF NOT EXISTS users
(
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(255) NOT NULL,
    email   VARCHAR(255),
    block   BOOLEAN      NOT NULL DEFAULT 0
);

INSERT INTO users (name, email, block)
VALUES ('Alice', 'alice@example.com', 0),
       ('Bob', 'bob@example.com', 1),
       ('Charlie', 'charlie@example.com', 0);