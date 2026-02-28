-- Database Export for Hostinger (MySQL/MariaDB)
-- Database Name: (create one in Hostinger and import this file)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` text NOT NULL,
  `full_name` varchar(100) DEFAULT 'Administrador',
  `profile_image` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `users`
-- Default: admin / admin123
INSERT INTO `users` (`username`, `password_hash`, `full_name`) VALUES
('admin', '$2y$10$wU0M/oGkLgE/tF.e.zP/Eu8RScNfVnE/M9f5XGv.r03r0ccw6lf', 'Administrador');

-- --------------------------------------------------------

-- Table structure for table `leads`
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `pix_code` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `amount` float DEFAULT 0,
  `step` varchar(50) DEFAULT 'start',
  `gateway` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `gateways`
CREATE TABLE IF NOT EXISTS `gateways` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT 0,
  `config_json` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `gateways`
INSERT INTO `gateways` (`name`, `active`) VALUES
('Perfect Pay', 0),
('Kirvano', 0),
('Paggue', 0),
('Genesys', 0),
('Amplo', 1);

-- --------------------------------------------------------

-- Table structure for table `recovery_rules`
CREATE TABLE IF NOT EXISTS `recovery_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delay_minutes` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `settings`
CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
