-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS nsct CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE nsct;

-- Create members table
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    gender ENUM('Male', 'Female', 'Other'),
    dob DATE,
    pan VARCHAR(10),
    photo VARCHAR(255),
    id_proof VARCHAR(255),
    state VARCHAR(50),
    district VARCHAR(50),
    country VARCHAR(50) DEFAULT 'India',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'editor',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('registration', 'donation', 'other') NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    receipt_image VARCHAR(255),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create sahyog (support/help) table
CREATE TABLE IF NOT EXISTS sahyog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    amount_needed DECIMAL(10,2) NOT NULL,
    amount_collected DECIMAL(10,2) DEFAULT 0.00,
    beneficiary_name VARCHAR(100) NOT NULL,
    beneficiary_details TEXT,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Create sahyog_contributions table
CREATE TABLE IF NOT EXISTS sahyog_contributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sahyog_id INT NOT NULL,
    member_id INT,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(100),
    payment_method VARCHAR(50) NOT NULL,
    receipt_image VARCHAR(255),
    contribution_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sahyog_id) REFERENCES sahyog(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Create password_reset table
CREATE TABLE IF NOT EXISTS password_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create id_cards table
CREATE TABLE IF NOT EXISTS id_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    card_number VARCHAR(50) UNIQUE,
    issue_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create vywastha_shulk (management fee) table
CREATE TABLE IF NOT EXISTS vywastha_shulk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    receipt_image VARCHAR(255),
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE members 
ADD COLUMN aadhar_number VARCHAR(12) NULL,
ADD COLUMN father_name VARCHAR(100) NULL,
ADD COLUMN nominee_name VARCHAR(100) NULL,
ADD COLUMN family_members INT NULL,
ADD COLUMN medical_condition TEXT NULL;

-- Create self_declarations table
CREATE TABLE IF NOT EXISTS self_declarations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    declaration_text TEXT NOT NULL,
    declaration_date DATE NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('active', 'revoked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admins (username, password, email, name, role) 
VALUES ('admin', '$2y$10$8WxSB.7wPrT4NJsU.3QNAOKpY4JbPm/i5NOEDc.MmWp.MKU9YgDLW', 'admin@nsct.com', 'Administrator', 'super_admin')
ON DUPLICATE KEY UPDATE id=id;

-- Create a view for active members
CREATE OR REPLACE VIEW active_members AS
SELECT id, name, mobile, email, address, gender, dob, pan, photo, state, district, country
FROM members
WHERE status = 'active';

CREATE TABLE `otp_verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
