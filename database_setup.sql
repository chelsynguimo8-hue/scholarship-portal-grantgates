CREATE DATABASE IF NOT EXISTS grantgates CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grantgates;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS scholarships;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    institution VARCHAR(100),
    program VARCHAR(100),
    gpa DECIMAL(3,2),
    year_of_study ENUM('1', '2', '3', '4', '5') DEFAULT '1',
    profile_picture_path VARCHAR(500) DEFAULT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE scholarships (
    scholarship_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    eligibility TEXT NOT NULL,
    amount DECIMAL(10,2),
    deadline DATE NOT NULL,
    status ENUM('active', 'expired', 'draft') DEFAULT 'active',
    created_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE applications (
    application_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    scholarship_id INT(11) NOT NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending',
    remarks TEXT,
    reviewed_by INT(11),
    reviewed_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(scholarship_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE documents (
    document_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    application_id INT(11) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    document_category VARCHAR(50) NOT NULL DEFAULT 'supporting_document',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE
);

CREATE TABLE password_resets (
    reset_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password_hash, first_name, last_name, role)
VALUES (
    'admin',
    'admin@scholarship.com',
    '$2y$10$CKWlAb/45mgOO3YZip1qsuzIoz5HdfVi9QA/nAWvheGbAG.CbDIoW',
    'System',
    'Administrator',
    'admin'
);

INSERT INTO scholarships (title, description, eligibility, amount, deadline, status) VALUES
('Merit Scholarship for Engineering', 'For outstanding engineering students with excellent academic records', 'Engineering students with GPA > 3.5', 500000, '2025-09-30', 'active'),
('Need-Based Scholarship', 'For students from low-income backgrounds', 'Household income < 500,000 FCFA per month', 300000, '2025-10-15', 'active'),
('Women in Tech Scholarship', 'Encouraging women to pursue technology careers', 'Female students in IT or Computer Science', 400000, '2025-10-31', 'active');
