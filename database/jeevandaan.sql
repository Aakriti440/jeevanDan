-- JeevanDaan Database Schema
DROP DATABASE IF EXISTS jeevandaan;
CREATE DATABASE jeevandaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jeevandaan;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    weight DECIMAL(5,2),
    province VARCHAR(100),
    district VARCHAR(100),
    municipality VARCHAR(150),
    ward_no INT,
    tole VARCHAR(255),
    last_donation_date DATE,
    has_hiv TINYINT(1) DEFAULT 0,
    has_hepatitis_b TINYINT(1) DEFAULT 0,
    has_hepatitis_c TINYINT(1) DEFAULT 0,
    has_diabetes TINYINT(1) DEFAULT 0,
    has_hypertension TINYINT(1) DEFAULT 0,
    other_diseases TEXT,
    is_eligible TINYINT(1) DEFAULT 1,
    citizenship_front VARCHAR(255),
    citizenship_back VARCHAR(255),
    donation_certificate VARCHAR(255),
    blood_group_proof VARCHAR(255),
    is_verified TINYINT(1) DEFAULT 0,
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verified_by INT,
    verified_at DATETIME,
    rejection_reason TEXT,
    willing_to_donate TINYINT(1) DEFAULT 1,
    receive_notifications TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB;

-- Organization Personnel Table
CREATE TABLE organization_personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    phone VARCHAR(20),
    organization_name VARCHAR(255),
    organization_id VARCHAR(100),
    organization_id_document VARCHAR(255), 
    organization_type ENUM('red_cross', 'hospital', 'blood_bank', 'ngo', 'other'),
    position VARCHAR(100),
    province VARCHAR(100),
    district VARCHAR(100),
    municipality VARCHAR(150),
    address TEXT,
    is_verified TINYINT(1) DEFAULT 0,
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verified_by INT,
    verified_at DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    receive_notifications TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB;

-- Admin Table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Password Reset Tokens
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    token VARCHAR(128) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Blood Requests Table
CREATE TABLE blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requested_by INT NOT NULL,
    requester_type ENUM('user', 'organization') NOT NULL,
    patient_name VARCHAR(255) NOT NULL,
    patient_age INT,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units_required INT DEFAULT 1,
    hospital_name VARCHAR(255) NOT NULL,
    hospital_district VARCHAR(100) NOT NULL,
    hospital_address TEXT,
    urgency ENUM('critical', 'urgent', 'normal') DEFAULT 'normal',
    status ENUM('active', 'fulfilled', 'cancelled', 'expired') DEFAULT 'active',
    contact_name VARCHAR(255),
    contact_phone VARCHAR(20) NOT NULL,
    contact_email VARCHAR(255),
    reason TEXT,
    required_by DATETIME,
    additional_notes TEXT,
    fulfilled_by INT,
    fulfilled_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Donation Records
CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    donation_date DATE NOT NULL,
    units INT DEFAULT 1,
    location VARCHAR(255),
    status ENUM('recorded', 'testing', 'accepted', 'rejected') DEFAULT 'recorded',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Appointment Management
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    donor_id INT NOT NULL,
    organization_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    location VARCHAR(255),
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organization_personnel(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Blood Testing Results
CREATE TABLE blood_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    organization_id INT NOT NULL,
    result ENUM('pending', 'safe', 'unsafe') DEFAULT 'pending',
    notes TEXT,
    tested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organization_personnel(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Blood Inventory
CREATE TABLE blood_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units_available INT DEFAULT 0,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_org_blood_group (organization_id, blood_group),
    FOREIGN KEY (organization_id) REFERENCES organization_personnel(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    recipient_type ENUM('user', 'organization', 'admin') NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Nepal Districts
CREATE TABLE nepal_districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    province VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Insert Districts
INSERT INTO nepal_districts (province, district) VALUES
('Koshi', 'Bhojpur'), ('Koshi', 'Dhankuta'), ('Koshi', 'Ilam'), ('Koshi', 'Jhapa'),
('Koshi', 'Khotang'), ('Koshi', 'Morang'), ('Koshi', 'Okhaldhunga'), ('Koshi', 'Panchthar'),
('Koshi', 'Sankhuwasabha'), ('Koshi', 'Solukhumbu'), ('Koshi', 'Sunsari'), ('Koshi', 'Taplejung'),
('Koshi', 'Terhathum'), ('Koshi', 'Udayapur'),
('Madhesh', 'Bara'), ('Madhesh', 'Dhanusha'), ('Madhesh', 'Mahottari'), ('Madhesh', 'Parsa'),
('Madhesh', 'Rautahat'), ('Madhesh', 'Saptari'), ('Madhesh', 'Sarlahi'), ('Madhesh', 'Siraha'),
('Bagmati', 'Bhaktapur'), ('Bagmati', 'Chitwan'), ('Bagmati', 'Dhading'), ('Bagmati', 'Dolakha'),
('Bagmati', 'Kathmandu'), ('Bagmati', 'Kavrepalanchok'), ('Bagmati', 'Lalitpur'), ('Bagmati', 'Makwanpur'),
('Bagmati', 'Nuwakot'), ('Bagmati', 'Ramechhap'), ('Bagmati', 'Rasuwa'), ('Bagmati', 'Sindhuli'),
('Bagmati', 'Sindhupalchok'),
('Gandaki', 'Baglung'), ('Gandaki', 'Gorkha'), ('Gandaki', 'Kaski'), ('Gandaki', 'Lamjung'),
('Gandaki', 'Manang'), ('Gandaki', 'Mustang'), ('Gandaki', 'Myagdi'), ('Gandaki', 'Nawalpur'),
('Gandaki', 'Parbat'), ('Gandaki', 'Syangja'), ('Gandaki', 'Tanahun'),
('Lumbini', 'Arghakhanchi'), ('Lumbini', 'Banke'), ('Lumbini', 'Bardiya'), ('Lumbini', 'Dang'),
('Lumbini', 'Gulmi'), ('Lumbini', 'Kapilvastu'), ('Lumbini', 'Parasi'), ('Lumbini', 'Palpa'),
('Lumbini', 'Pyuthan'), ('Lumbini', 'Rolpa'), ('Lumbini', 'Rukum East'), ('Lumbini', 'Rupandehi'),
('Karnali', 'Dailekh'), ('Karnali', 'Dolpa'), ('Karnali', 'Humla'), ('Karnali', 'Jajarkot'),
('Karnali', 'Jumla'), ('Karnali', 'Kalikot'), ('Karnali', 'Mugu'), ('Karnali', 'Rukum West'),
('Karnali', 'Salyan'), ('Karnali', 'Surkhet'),
('Sudurpashchim', 'Achham'), ('Sudurpashchim', 'Baitadi'), ('Sudurpashchim', 'Bajhang'),
('Sudurpashchim', 'Bajura'), ('Sudurpashchim', 'Dadeldhura'), ('Sudurpashchim', 'Darchula'),
('Sudurpashchim', 'Doti'), ('Sudurpashchim', 'Kailali'), ('Sudurpashchim', 'Kanchanpur');

-- Insert Default Admin (password: Admin@123)
INSERT INTO admins (username, email, password, full_name, role) VALUES
('admin', 'admin@jeevandaan.org.np', '$2y$12$.GlCZwQCee8Y3sjrKSOxT.NGJmyQ1wz5.Js.i6XFjIHtKSVIX8blq', 'System Administrator', 'super_admin');

-- Insert Sample Blood Request
INSERT INTO blood_requests (requested_by, requester_type, patient_name, patient_age, blood_group, units_required, hospital_name, hospital_district, urgency, contact_name, contact_phone, reason, status) VALUES
(1, 'user', 'Ram Bahadur', 45, 'A+', 2, 'Bir Hospital', 'Kathmandu', 'critical', 'Sita Sharma', '9841234567', 'Surgery', 'active'),
(1, 'user', 'Gita Kumari', 32, 'B+', 1, 'Grande Hospital', 'Lalitpur', 'urgent', 'Hari Prasad', '9851234567', 'Accident', 'active'),
(1, 'user', 'Bishnu Thapa', 28, 'O-', 3, 'Civil Hospital', 'Morang', 'critical', 'Krishna Thapa', '9812345678', 'Emergency', 'active');

