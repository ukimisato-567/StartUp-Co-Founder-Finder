
CREATE DATABASE IF NOT EXISTS scf_db;
USE scf_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    skills VARCHAR(255),
    bio TEXT,
    avatar VARCHAR(255),
    role ENUM('user','admin') DEFAULT 'user',
    status ENUM('active','suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE startups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50),
    skills_needed VARCHAR(255),
    image VARCHAR(255),
    status ENUM('open','closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
INSERT INTO users (uid, name, email, password, skills, role)
VALUES ('SCF1001', 'Admin', 'admin@scf.com', '$2y$10$zTg64cK81vmvjFMKyq7XK.WTzCJUw2URDmYHJMuIhXUrz50bQJM8G', 'Administration', 'admin');
