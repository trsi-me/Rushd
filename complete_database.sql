-- Complete Database Setup for Rushd Expense/Income Tracker
-- Run this file in MySQL to create the complete database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop database if exists (optional - remove if you want to keep existing data)
-- DROP DATABASE IF EXISTS `u741730784_rushed`;

-- Create database
CREATE DATABASE IF NOT EXISTS `u741730784_rushed` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `u741730784_rushed`;

-- Drop tables if they exist
DROP TABLE IF EXISTS `transaction_table`;
DROP TABLE IF EXISTS `users`;

-- Create users table for authentication
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL UNIQUE,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create transaction_table with user_id column
CREATE TABLE `transaction_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` VARCHAR(50) NULL,
  `user_id` int(11) NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `transaction_table_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=27;

-- Insert default users
-- Admin user (password: admin123)
INSERT INTO `users` (`username`, `email`, `password`) 
VALUES 
('admin', 'admin@rushd.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
-- Regular user (password: user123)
('user', 'user@rushd.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'),
-- Test user (password: test123)
('test', 'test@rushd.com', '$2y$10$dXJ3SW6G7P50lGmMkkmwe.20cQQubK3.H/Hh0qGqJxJqJqJqJqJqJ');

-- Insert default transactions for admin user (user_id = 1)
INSERT INTO `transaction_table` (`transaction_type`, `description`, `category`, `amount`, `user_id`) 
VALUES
('Income', 'Salary', NULL, '5000.00', 1),
('Income', 'Freelance Project', NULL, '1200.00', 1),
('Expense', 'Rent', 'Essential', '1500.00', 1),
('Expense', 'Groceries', 'Essential', '350.00', 1),
('Expense', 'Gym Membership', 'Luxury', '80.00', 1),
('Expense', 'Restaurant', 'Luxury', '120.00', 1),
('Income', 'Investment Return', NULL, '500.00', 1),
('Expense', 'Transportation', 'Essential', '200.00', 1),
('Expense', 'Utilities', 'Essential', '150.00', 1),
('Income', 'Side Job', NULL, '800.00', 1);

-- Insert default transactions for user (user_id = 2)
INSERT INTO `transaction_table` (`transaction_type`, `description`, `category`, `amount`, `user_id`) 
VALUES
('Income', 'Part-time Job', NULL, '2000.00', 2),
('Expense', 'Rent', 'Essential', '800.00', 2),
('Expense', 'Food', 'Essential', '300.00', 2),
('Expense', 'Entertainment', 'Luxury', '100.00', 2),
('Income', 'Gift Money', NULL, '200.00', 2);

COMMIT;
