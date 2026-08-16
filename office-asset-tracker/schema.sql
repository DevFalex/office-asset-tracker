-- Office Asset Tracker — deployment schema + seed data.
-- Idempotent: safe to run against an empty or already-initialised database.
-- Loaded automatically by db.php on first run against a fresh database.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(100) NOT NULL,
  `asset_type` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `status` enum('Available','In Use','Under Repair','Disposed') DEFAULT 'Available',
  PRIMARY KEY (`asset_id`),
  UNIQUE KEY `serial_number` (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `asset_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `assigned_date` date DEFAULT curdate(),
  `return_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `asset_id` (`asset_id`),
  KEY `staff_id` (`staff_id`),
  CONSTRAINT `asset_assignments_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`asset_id`),
  CONSTRAINT `asset_assignments_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data ---------------------------------------------------------------

INSERT IGNORE INTO `assets` (`asset_id`, `asset_name`, `asset_type`, `serial_number`, `purchase_date`, `status`) VALUES
(1, 'Dell Latitude 5420 Laptop', 'Laptop', 'DL5420-001', '2023-01-15', 'In Use'),
(2, 'HP LaserJet Pro M404dn', 'Printer', 'HP-M404-PRT-12', '2022-11-05', 'In Use'),
(3, 'Samsung 27\" Monitor', 'Monitor', 'SMNTR-27-445', '2023-03-20', 'Available'),
(4, 'Logitech Wireless Mouse', 'Peripheral', 'LGMSE-WR-778', '2024-01-10', 'In Use'),
(5, 'Cisco Router 2901', 'Networking', 'CISCO-2901-XY', '2022-06-14', 'Under Repair'),
(6, 'Lenovo ThinkPad X1 Carbon', 'Laptop', 'LN-X1C-889', '2024-02-18', 'In Use'),
(7, 'Epson Projector EB-S41', 'Projector', 'EPSN-PJ-556', '2021-12-22', 'Disposed'),
(8, 'APC Smart-UPS 1500VA', 'Power Backup', 'APC-UPS-1500', '2023-08-10', 'In Use'),
(9, 'Canon ImageRunner C3220', 'Copier', 'CNC-C3220-99', '2022-09-01', 'Available'),
(10, 'Apple MacBook Pro 16', 'Laptop', 'MBP16-2023-77', '2025-09-01', 'Available');

-- Passwords are md5 hashes (matching the original app). admin => mit_admin123
INSERT IGNORE INTO `users` (`user_id`, `username`, `password`, `role`, `full_name`, `department`) VALUES
(1, 'admin', '879e5d641efa526af6f1a3b8e9868927', 'Admin', 'System Admin', 'IT'),
(5, 'Falex', '18228d936ed470784aae78cd39c82c72', 'Staff', 'Opeoluwa Faleye', 'Computer Science');

INSERT IGNORE INTO `asset_assignments` (`assignment_id`, `asset_id`, `staff_id`, `assigned_date`, `return_date`, `remarks`) VALUES
(3, 1, 5, '2025-09-02', NULL, NULL),
(4, 8, 5, '2025-09-02', NULL, NULL);
