--
-- Database Schema for WebReporter PHP
-- Use this script to set up the necessary tables for authentication and access control.
--

-- Table: user_table
-- Holds user credentials. Passwords should be stored in MD5 format for compatibility.
CREATE TABLE IF NOT EXISTS `user_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,    -- Administrator status (1: Admin, 0: Regular User)
  `enabled` tinyint(4) NOT NULL DEFAULT 1,       -- Global account status (1: Active, 0: Disabled)
  `has_report_access` tinyint(1) NOT NULL DEFAULT 1, -- Permission to use this reporting system
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert a default administrator (username: admin, password: admin_password)
-- Note: Replace with a secure MD5 hash before deploying.
-- INSERT INTO `user_table` (`username`, `password`, `email`, `is_admin`, `enabled`, `has_report_access`) 
-- VALUES ('admin', MD5('admin_password'), 'admin@example.com', 1, 1, 1);
