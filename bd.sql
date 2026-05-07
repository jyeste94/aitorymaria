CREATE DATABASE IF NOT EXISTS boda_aitorymaria
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE boda_aitorymaria;

CREATE TABLE IF NOT EXISTS rsvp_guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attendance VARCHAR(10) NOT NULL COMMENT 'yes o no',
    name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    companions INT DEFAULT 0,
    companions_details TEXT DEFAULT NULL,
    meat INT DEFAULT 0,
    fish INT DEFAULT 0,
    songs TEXT DEFAULT NULL,
    comments TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
