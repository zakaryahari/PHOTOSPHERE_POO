create database PhotoShere;

use PhotoShere;


CREATE TABLE User (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL, 
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    bio TEXT, 
    profile_picture VARCHAR(255), 
    created_at DATETIME DEFAULT CURRENT_DATE, 
    last_login DATETIME 
);

CREATE TABLE Basic_User (
    id_user INT PRIMARY KEY,
    upload_count INT DEFAULT 0, 
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);


CREATE TABLE Pro_User (
    id_user INT PRIMARY KEY,
    subscription_start DATETIME, 
    subscription_end DATETIME, 
    CONSTRAINT chk_dates CHECK (subscription_start < subscription_end),
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);


CREATE TABLE Moderator (
    id_user INT PRIMARY KEY,
    level ENUM('junior', 'senior', 'lead'), 
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);


CREATE TABLE Admin (
    id_user INT PRIMARY KEY,
    is_super BOOLEAN DEFAULT FALSE, 
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE
);