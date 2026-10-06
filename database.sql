-- Startup Co-Founder Finder - PHASE 2 database (Phase 1 tables + requests, teams, chat)
-- Import this in phpMyAdmin (Import tab). It drops the old scf_db first, so it also upgrades a Phase 1 database.

DROP DATABASE IF EXISTS scf_db;
CREATE DATABASE scf_db;
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

CREATE TABLE join_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    startup_id INT NOT NULL,
    user_id INT NOT NULL,
    message VARCHAR(255),
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (startup_id) REFERENCES startups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (startup_id, user_id)
);

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    startup_id INT NOT NULL UNIQUE,
    team_name VARCHAR(150) NOT NULL,
    goal VARCHAR(255),
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (startup_id) REFERENCES startups(id) ON DELETE CASCADE
);

CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    role_in_team VARCHAR(50) DEFAULT 'Member',
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (team_id, user_id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- admin account: admin@scf.com / Admin@123
INSERT INTO users (uid, name, email, password, skills, role)
VALUES ('SCF1001', 'Admin', 'admin@scf.com', '$2y$10$zTg64cK81vmvjFMKyq7XK.WTzCJUw2URDmYHJMuIhXUrz50bQJM8G', 'Administration', 'admin');
