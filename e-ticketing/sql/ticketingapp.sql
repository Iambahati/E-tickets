DROP DATABASE IF EXISTS `e-ticketing`;
-- --------------------------------------------------------
-- Create the database
-- --------------------------------------------------------
CREATE DATABASE `e-ticketing` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- Use the newly created database
USE `e-ticketing`;
-- New SCHEMA
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  -- Nullable first name
  `last_name` varchar(50) DEFAULT NULL,
  -- Nullable last name
  `email` varchar(50) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `organization_name` varchar(100) DEFAULT NULL,
  -- Nullable organization name
  `role` enum('1', '2', '3') NOT NULL DEFAULT '3',
  -- 1 = admin, 2 = organizer, 3 = buyer
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE events (
  event_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  organizer_id INT,
  description TEXT,
  location_details VARCHAR(255) NOT NULL,
  ticket_price int(11) NOT NULL,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  event_status  ENUM ('ACTIVE', 'INACTIVE', 'DELETED') DEFAULT 'INACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  event_type VARCHAR(255) NOT NULL,
  event_photo VARCHAR(255) NOT NULL,
  ticket_quantity_available INT NOT NULL,
  is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (organizer_id) REFERENCES users(id)
);
-- CREATE TABLE tickets (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     event_id INT,
--     title VARCHAR(255) NOT NULL,
--     ticket_type VARCHAR(100) NOT NULL,
--     amount DECIMAL(10, 2) NOT NULL,
--     hide_when_sold_out BOOLEAN NOT NULL DEFAULT TRUE,
--     show_quantity_remaining BOOLEAN NOT NULL DEFAULT FALSE,
--     is_deleated BOOLEAN NOT NULL DEFAULT FALSE,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--     deleated_at TIMESTAMP,
--     type ENUM('FREE', 'PAID', 'TIERED') NOT NULL DEFAULT 'PAID',
--     FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
-- );
CREATE TABLE tickets (
  ticket_id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  customer_id INT NOT NULL,
  purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  ticket_status ENUM('Purchased', 'Pending', 'Cancelled') DEFAULT 'Purchased',
  quantity INT NOT NULL,
  FOREIGN KEY (event_id) REFERENCES events(event_id),
  FOREIGN KEY (customer_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS `logs` (
  `id` BIGINT(20) PRIMARY KEY AUTO_INCREMENT,
  `service_number` MEDIUMINT(7) UNSIGNED NOT NULL,
  `action` VARCHAR(255) NOT NULL,
  `type` ENUM(
    'Register',
    'Log In',
    'Log Out',
    'Reset Password',
    'Create Event',
    'Update Event',
    'Delete Event',
    'Activate Event',
    'Deactivate Event',
    'Add Ticket Type',
    'Update Ticket Type',
    'Delete Ticket Type',
    'Purchase Ticket',
    'Refund Ticket',
    'Check In Attendee',
    'View Event Insights',
    'Generate Report',
    'Add Payment',
    'Update Payment',
    'Cancel Payment',
    'General Log'
  ) NOT NULL DEFAULT 'General Log',
  `ip_address` VARCHAR(15) DEFAULT NULL,
  `time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP()
);