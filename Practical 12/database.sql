CREATE DATABASE IF NOT EXISTS studenthub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE studenthub;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    poster VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
