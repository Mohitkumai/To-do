-- This script sets up the database and table for the To-Do List application.
-- You can run this in a tool like phpMyAdmin.
-- 1. Create the database (if it doesn't exist)
-- It's often better to create the database manually through your hosting panel or phpMyAdmin.
CREATE DATABASE IF NOT EXISTS `todo_app_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Select the database to use
USE `todo_app_db`;
-- 3. Create the 'tasks' table
-- This table will store all the to-do items.
CREATE TABLE `tasks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `task` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- 4. (Optional) Insert some sample data to start with
INSERT INTO `tasks` (`task`, `is_completed`) VALUES
('Set up the project environment', 1),
('Create the database and table', 1),
('Build the PHP backend API', 0),
('Develop the HTML and JavaScript frontend', 0);
