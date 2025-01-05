CREATE DATABASE ootd_user_data_base;

USE ootd_user_data_base;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN pseudo VARCHAR(255) NULL;
UPDATE users SET pseudo = 'TITITOTO' WHERE id = 5;

SELECT * FROM users;
CREATE TABLE images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255),
    color VARCHAR(50),
    style VARCHAR(50)
);
CREATE TABLE user_preferences (
    user_id INT,
    image_id INT,
    liked TINYINT(1),
    PRIMARY KEY(user_id, image_id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(image_id) REFERENCES images(image_id)
);
INSERT INTO images (image_url, color, style)
VALUES
('image/Decontracte/1.jpg', 'marron', 'décontracté'),
('image/Decontracte/2.jpg', 'gris', 'décontracté'),
('image/Decontracte/3.jpg', 'noir', 'décontracté'),
('image/Decontracte/4.jpg', 'gris', 'décontracté'),
('image/Decontracte/5.jpg', 'noir', 'décontracté'),
('image/Decontracte/6.jpg', 'bleu', 'décontracté'),
('image/Decontracte/7.jpg', 'marron', 'décontracté'),
('image/Decontracte/8.jpg', 'noir', 'décontracté'),
('image/Decontracte/9.jpg', 'marron', 'décontracté'),
('image/Decontracte/10.jpg', 'marron', 'décontracté'),
('image/Chic/1.jpg', 'beige', 'chic'),
('image/Chic/2.jpg', 'vert', 'chic'),
('image/Chic/3.jpg', 'gris', 'chic'),
('image/Chic/4.jpg', 'rouge', 'chic'),
('image/Chic/5.jpg', 'noir', 'chic'),
('image/Chic/6.jpg', 'blanc', 'chic'),
('image/Chic/7.jpg', 'blanc', 'chic'),
('image/Chic/8.jpg', 'noir', 'chic'),
('image/Chic/9.jpg', 'gris', 'chic'),
('image/Chic/10.jpg', 'marron', 'chic'),
('image/Streetwear/1.jpg', 'bleu', 'streetwear'),
('image/Streetwear/2.jpg', 'gris', 'streetwear'),
('image/Streetwear/3.jpg', 'marron', 'streetwear'),
('image/Streetwear/4.jpg', 'marron', 'streetwear'),
('image/Streetwear/5.jpg', 'blanc', 'streetwear'),
('image/Streetwear/6.jpg', 'noir', 'streetwear'),
('image/Streetwear/7.jpg', 'marron', 'streetwear'),
('image/Streetwear/8.jpg', 'noir', 'streetwear'),
('image/Streetwear/9.jpg', 'vert', 'streetwear'),
('image/Streetwear/10.jpg', 'rouge', 'streetwear'),
('image/Boheme/1.jpg', 'kaki', 'boheme'),
('image/Boheme/2.jpg', 'vert', 'boheme'),
('image/Boheme/3.jpg', 'blanc', 'boheme'),
('image/Boheme/4.jpg', 'rouge', 'boheme'),
('image/Boheme/5.jpg', 'marron', 'boheme'),
('image/Boheme/6.jpg', 'violet', 'boheme'),
('image/Boheme/7.jpg', 'marron', 'boheme'),
('image/Boheme/8.jpg', 'rouge', 'boheme'),
('image/Boheme/9.jpg', 'rouge', 'boheme'),
('image/Boheme/10.jpg', 'blanc', 'boheme');

