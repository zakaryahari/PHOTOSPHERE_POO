create database PhotoShere;

use PhotoShere;


CREATE TABLE User (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL, 
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    bio TEXT, 
    profile_picture VARCHAR(255), 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
    last_login DATETIME 
);

CREATE TABLE Basic_User (
    id_user INT PRIMARY KEY,
    upload_count INT DEFAULT 0, 
    FOREIGN KEY (id_user) REFERENCES User(id_user) 
);


CREATE TABLE Pro_User (
    id_user INT PRIMARY KEY,
    subscription_start DATETIME, 
    subscription_end DATETIME, 
    CONSTRAINT chk_dates CHECK (subscription_start < subscription_end),
    FOREIGN KEY (id_user) REFERENCES User(id_user) 
);


CREATE TABLE Moderator (
    id_user INT PRIMARY KEY,
    level ENUM('junior', 'senior', 'lead'), 
    FOREIGN KEY (id_user) REFERENCES User(id_user) 
);


CREATE TABLE Admin (
    id_user INT PRIMARY KEY,
    is_super BOOLEAN DEFAULT FALSE, 
    FOREIGN KEY (id_user) REFERENCES User(id_user) 
);

CREATE TABLE Photo (
    id_photo INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file_name VARCHAR(255) UNIQUE NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    dimensions VARCHAR(50) NOT NULL,
    state ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    view_count INT DEFAULT 0,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES User(id_user) 
);

CREATE TABLE Album (
    id_album INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT TRUE,
    cover_photo_path VARCHAR(255),
    photo_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES User(id_user) 
);

CREATE TABLE Tag (
    id_tag INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    usage_count INT DEFAULT 0
);

CREATE TABLE Comment (
    id_comment INT PRIMARY KEY AUTO_INCREMENT,
    content TEXT NOT NULL,
    is_edited BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    id_user INT NOT NULL,
    id_photo INT NOT NULL,
    parent_id INT,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ,
    FOREIGN KEY (id_photo) REFERENCES Photo(id_photo) ,
    FOREIGN KEY (parent_id) REFERENCES Comment(id_comment) ON DELETE SET NULL
);

CREATE TABLE Likes (
    id_user INT ,
    id_photo INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user, id_photo),
    FOREIGN KEY (id_user) REFERENCES User(id_user) ,
    FOREIGN KEY (id_photo) REFERENCES Photo(id_photo) 
);

CREATE TABLE Photo_Albums (
    id_photo INT,
    id_album INT,
    PRIMARY KEY (id_photo, id_album),
    FOREIGN KEY (id_photo) REFERENCES Photo(id_photo) ,
    FOREIGN KEY (id_album) REFERENCES Album(id_album) 
);

CREATE TABLE Photo_Tags (
    id_photo INT,
    id_tag INT,
    PRIMARY KEY (id_photo, id_tag),
    FOREIGN KEY (id_photo) REFERENCES Photo(id_photo) ,
    FOREIGN KEY (id_tag) REFERENCES Tag(id_tag) 
);

CREATE TABLE Audit_Log (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(100) NOT NULL,
    ip_source VARCHAR(45) NOT NULL,
    reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_user INT,
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE SET NULL
);

ALTER TABLE User ADD COLUMN role ENUM('basic', 'pro', 'moderator', 'admin') DEFAULT 'basic';
