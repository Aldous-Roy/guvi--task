-- Database: internship_db
-- 
-- This schema defines the structure for the 'users' table, which stores user
-- account information for the application.
--
-- Table: users
--   - id:            Unique identifier for each user (Primary Key, Auto Increment).
--   - email:         User's email address (Unique, Required).
--   - password_hash: Hashed password for authentication (Required).
--   - full_name:     Full name of the user (Optional).
--   - age:           Age of the user (Optional).
--   - dob:           Date of birth of the user (Optional).
--   - contact:       Contact information, such as phone number (Optional).
--   - created_at:    Timestamp when the user record was created (Defaults to current time).
--   - updated_at:    Timestamp when the user record was last updated (Auto-updates on modification).
--
-- Usage:
--   - Stores essential and optional user profile information.
--   - Supports user authentication and profile management.
CREATE DATABASE IF NOT EXISTS internship_db;
USE internship_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(255),
  age INT,
  dob DATE,
  contact VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);