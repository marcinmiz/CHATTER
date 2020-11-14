
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Database: `db_chat`

-- `users` table structure

CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(11) NOT NULL PRIMARY KEY UNIQUE KEY AUTO_INCREMENT,
  `user_name` VARCHAR(40) COLLATE utf8_polish_ci NOT NULL,
  `surname` VARCHAR(40) COLLATE utf8_polish_ci NOT NULL,
  `email` VARCHAR(40) COLLATE utf8_polish_ci NOT NULL,
  `password` VARCHAR(255) COLLATE utf8_polish_ci NOT NULL,
  `activation_token` VARCHAR(255) COLLATE utf8_polish_ci NOT NULL,
  `account_active` boolean NOT NULL,
  `joined` DATETIME NOT NULL,
  `last_activity` TIMESTAMP NOT NULL
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

CREATE TABLE IF NOT EXISTS `users_sessions` (
`session_id` int(11) NOT NULL PRIMARY KEY UNIQUE KEY AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`hash` VARCHAR(50) NOT NULL
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
-- `users` table structure

CREATE TABLE IF NOT EXISTS `groups` (
`group_id` int(11) NOT NULL PRIMARY KEY UNIQUE KEY AUTO_INCREMENT,
  `group_name` VARCHAR(40) COLLATE utf8_polish_ci NOT NULL,
  `group_active` boolean NOT NULL
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- `group members` table structure

CREATE TABLE IF NOT EXISTS `group_members` (
`group_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
PRIMARY KEY(group_id,user_id),
  FOREIGN KEY(group_id) REFERENCES groups(group_id),
  FOREIGN KEY(user_id) REFERENCES users(user_id)
);

-- `private_messages` table structure

CREATE TABLE IF NOT EXISTS `private_messages` (
`message_id` int(11) NOT NULL PRIMARY KEY UNIQUE KEY AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` VARCHAR(255) NOT NULL,
  `sent_date` DATETIME NOT NULL,
  `new` boolean NOT NULL,
  FOREIGN KEY(sender_id) REFERENCES users(user_id),
  FOREIGN KEY(receiver_id) REFERENCES users(user_id)
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- `group_messages` table structure

CREATE TABLE IF NOT EXISTS `group_messages` (
`message_id` int(11) NOT NULL PRIMARY KEY UNIQUE KEY AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` VARCHAR(255) NOT NULL,
  `sent_date` DATETIME NOT NULL,
  `new` boolean NOT NULL,
  FOREIGN KEY(sender_id) REFERENCES users(user_id),
  FOREIGN KEY(receiver_id) REFERENCES groups(group_id)
) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- `receivers` table structure

CREATE TABLE IF NOT EXISTS `receivers` (
`receiver_id` int(11) NOT NULL PRIMARY KEY UNIQUE KEY AUTO_INCREMENT,
`receiver_type` VARCHAR(1) NOT NULL,

) AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
-- `favourite_users` table structure

CREATE TABLE IF NOT EXISTS `favourite_users` (
`liker_user_id` int(11) NOT NULL,
`popular_user_id` int(11) NOT NULL,
PRIMARY KEY(liker_user_id,popular_user_id),
  FOREIGN KEY(liker_user_id) REFERENCES users(user_id),
  FOREIGN KEY(popular_user_id) REFERENCES users(user_id)
);
