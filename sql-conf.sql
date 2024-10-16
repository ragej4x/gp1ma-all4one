CREATE DATABASE gp1ma_db;
USE gp1ma_db;

CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE groups (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_by INT(11) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE group_members (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id INT(11) UNSIGNED NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (group_id, user_id) 
) ENGINE=InnoDB;

CREATE TABLE messages (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id INT(11) UNSIGNED NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;



/* MANUAL INPUT PAG D GUMANA */
`bio` TEXT DEFAULT NULL,
`profile_pic` VARCHAR(255) DEFAULT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

/*=========*/


CREATE TABLE `friends` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `friend_id` INT(11) UNSIGNED NOT NULL,
    `status` ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`friend_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `user_friend` (`user_id`, `friend_id`)
) ENGINE=InnoDB;
