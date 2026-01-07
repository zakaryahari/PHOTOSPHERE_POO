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

ALTER TABLE User ADD COLUMN status ENUM('active','archived') DEFAULT 'active';

ALTER TABLE Photo ADD COLUMN like_count INT DEFAULT 0;

ALTER TABLE Photo ADD COLUMN comment_count INT DEFAULT 0;


-- -- 1. Insert into User (The Parent)
-- INSERT INTO User (id_user, username, email, password_hash, bio, role, status) VALUES 
-- (1, 'zak_admin', 'admin@photosphere.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator.', 'admin', 'active'),
-- (2, 'morocco_mod', 'mod@photosphere.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Content Moderator.', 'moderator', 'active'),
-- (3, 'pro_photographer', 'pro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Professional Photographer.', 'pro', 'active'),
-- (4, 'hobby_user', 'hobby@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'I love landscape photos.', 'basic', 'active'),
-- (5, 'old_account', 'old@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'This user is archived.', 'basic', 'archived');

-- -- 2. Insert into Specific Role Tables (The Children)
-- INSERT INTO Admin (id_user, is_super) VALUES (1, TRUE);
-- INSERT INTO Moderator (id_user, level) VALUES (2, 'lead');
-- INSERT INTO Pro_User (id_user, subscription_start, subscription_end) VALUES (3, '2026-01-01 10:00:00', '2027-01-01 10:00:00');
-- INSERT INTO Basic_User (id_user, upload_count) VALUES (4, 2), (5, 8);

-- -- 3. Insert Albums
-- INSERT INTO Album (id_album, name, description, is_public, id_user) VALUES 
-- (1, 'Marrakech Streets', 'Photos of the red city.', TRUE, 3),
-- (2, 'Secret Projects', 'Not for public eyes.', FALSE, 1);

-- -- 4. Insert Photos
-- INSERT INTO Photo (id_photo, title, file_name, file_size, mime_type, dimensions, state, id_user) VALUES 
-- (1, 'Koutoubia Mosque', 'koutoubia.jpg', 2048000, 'image/jpeg', '1920x1080', 'published', 3),
-- (2, 'Majorelle Garden', 'majorelle.png', 3500000, 'image/png', '2048x2048', 'published', 3),
-- (3, 'Draft Photo', 'draft1.jpg', 1024000, 'image/jpeg', '800x600', 'draft', 4);

-- -- 5. Insert Tags
-- INSERT INTO Tag (id_tag, name, slug) VALUES (1, 'Nature', 'nature'), (2, 'City', 'city');

-- -- 6. Connect Photos to Tags & Albums
-- INSERT INTO Photo_Tags (id_photo, id_tag) VALUES (1, 2), (2, 1);
-- INSERT INTO Photo_Albums (id_photo, id_album) VALUES (1, 1), (2, 1);

-- -- 7. Social Interactions
-- INSERT INTO Likes (id_user, id_photo) VALUES (4, 1), (2, 1);
-- INSERT INTO Comment (content, id_user, id_photo, parent_id) VALUES 
-- ('Beautiful shot!', 4, 1, NULL),
-- ('What lens did you use?', 2, 1, NULL);

-- -- Threaded reply
-- INSERT INTO Comment (content, id_user, id_photo, parent_id) VALUES 
-- ('I used a 35mm f1.4.', 3, 1, 2);

-- -- 8. Audit Log
-- INSERT INTO Audit_Log (action, ip_source, reason, id_user) VALUES 
-- ('LOGIN', '192.168.1.1', 'Standard login', 1),
-- ('ARCHIVE_USER', '192.168.1.1', 'User requested deletion', 5);
