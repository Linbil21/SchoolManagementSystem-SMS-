USE sms;

CREATE TABLE IF NOT EXISTS users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'offline',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS login_logs (
    logId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS courses (
    courseId INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) NOT NULL,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    department VARCHAR(100)
);
 
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    mid_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100),
    year_level VARCHAR(50) DEFAULT 'First Year',
    status ENUM('Regular', 'Irregular', 'Transferee') DEFAULT 'Regular',
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS enrollments (
    enrollmentId INT AUTO_INCREMENT PRIMARY KEY,
    reference_code VARCHAR(20) UNIQUE NOT NULL,
    admission_type VARCHAR(50) NOT NULL,
    course_id INT,
    year_level VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    mid_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    birthdate DATE NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    birth_cert VARCHAR(255),
    form_138 VARCHAR(255),
    id_picture VARCHAR(255) NOT NULL,
    form_137 VARCHAR(255),
    good_moral VARCHAR(255),
    barangay_clearance VARCHAR(255),
    guardian_first VARCHAR(100) NOT NULL,
    guardian_middle VARCHAR(100),
    guardian_last VARCHAR(100) NOT NULL,
    guardian_email VARCHAR(255) NOT NULL,
    guardian_contact VARCHAR(20) NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    guardian_address TEXT NOT NULL,
    primary_school VARCHAR(255) NOT NULL,
    primary_year VARCHAR(10) NOT NULL,
    secondary_school VARCHAR(255) NOT NULL,
    secondary_year VARCHAR(10) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending Review',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(courseId)
);

-- Insert sample data if not exists
INSERT IGNORE INTO courses (courseId, course_name, course_code, department) VALUES 
(1, 'BS Information Technology', 'BSIT', 'College of Computer Studies'),
(2, 'BS Computer Science', 'BSCS', 'College of Computer Studies'),
(3, 'BS Business Administration', 'BSBA', 'College of Business'),
(4, 'BS Criminology', 'BS Crim', 'College of Criminology'),
(5, 'BS Hospitality Management', 'BSHM', 'College of Hospitality'),
(6, 'BS Accountancy', 'BSA', 'College of Accountancy'),
(7, 'BS Civil Engineering', 'BSCE', 'College of Engineering');

INSERT IGNORE INTO users (email, password, role, status) VALUES 
('admin@example.com', 'admin123', 'admin', 'offline'),
('admission@example.com', 'admission123', 'admission', 'offline'),
('cashier@example.com', 'cashier123', 'cashier', 'offline'),
('superadmin@example.com', 'superadmin123', 'superadmin', 'offline');

-- Example Enrollment Record (matches Picture 1)
INSERT IGNORE INTO enrollments (reference_code, admission_type, course_id, year_level, first_name, last_name, gender, birthdate, contact_number, email, address, id_picture, guardian_first, guardian_last, guardian_email, guardian_contact, relationship, guardian_address, primary_school, primary_year, secondary_school, secondary_year, status) 
VALUES ('ENR25000001', 'Freshman', 1, 'First Year', 'Miguel Enrique', 'UY', 'Male', '2005-05-15', '09123456789', 'miguel@example.com', '123 Manila St', 'uploads/id/enr25000001.png', 'Juan', 'UY', 'juan@example.com', '09998887766', 'Father', '123 Manila St', 'Manila Primary', '2017', 'Manila Secondary', '2023', 'Enrolled');

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  type VARCHAR(50) NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  profile_image VARCHAR(255) DEFAULT NULL,
  icon VARCHAR(100) DEFAULT 'fa-bell',
  icon_bg VARCHAR(50) DEFAULT '#eef2ff',
  icon_color VARCHAR(50) DEFAULT '#1648bc',
  is_read TINYINT(1) DEFAULT 0,
  link VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_read (user_id, is_read),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- New Tables for Advanced Modules
CREATE TABLE IF NOT EXISTS subjects (
    subjectId INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL UNIQUE,
    subject_name VARCHAR(255) NOT NULL,
    units INT NOT NULL,
    year_level VARCHAR(50),
    semester VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS sections (
    sectionId INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL,
    course_id INT,
    year_level VARCHAR(50),
    capacity INT DEFAULT 40,
    FOREIGN KEY (course_id) REFERENCES courses(courseId)
);

CREATE TABLE IF NOT EXISTS payments (
    paymentId INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    status VARCHAR(50) DEFAULT 'Pending',
    transaction_id VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollmentId)
);

CREATE TABLE IF NOT EXISTS roles_config (
    roleId INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS permissions (
    permissionId INT AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles_config(roleId),
    FOREIGN KEY (permission_id) REFERENCES permissions(permissionId)
);

-- Sample Data for New Tables
INSERT IGNORE INTO roles_config (role_name, description) VALUES 
('superadmin', 'Full system access'),
('admin', 'General administrative access'),
('registrar', 'Student and enrollment management'),
('admission', 'Admission processing');

INSERT IGNORE INTO subjects (subject_code, subject_name, units, year_level, semester) VALUES 
('IT111', 'Introduction to Computing', 3, 'First Year', 'First Semester'),
('IT112', 'Computer Programming 1', 3, 'First Year', 'First Semester'),
('MATH101', 'Calculus 1', 3, 'First Year', 'First Semester');

INSERT IGNORE INTO sections (section_name, course_id, year_level) VALUES 
('BSIT-1A', 1, 'First Year'),
('BSIT-1B', 1, 'First Year'),
('BSCS-1A', 2, 'First Year');

-- Additional Tables for Full Module Support
CREATE TABLE IF NOT EXISTS academic_years (
    ayId INT AUTO_INCREMENT PRIMARY KEY,
    school_year VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'inactive'
);

CREATE TABLE IF NOT EXISTS departments (
    deptId INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) UNIQUE NOT NULL,
    head_name VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS system_logs (
    logId INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(255),
    performed_by INT,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (performed_by) REFERENCES users(userId)
);

-- Sample Data for Completeness
INSERT IGNORE INTO academic_years (school_year, status) VALUES 
('2024-2025', 'inactive'),
('2025-2026', 'active');

INSERT IGNORE INTO departments (dept_name, head_name) VALUES 
('College of Computer Studies', 'Dr. Alan Turing'),
('College of Business', 'Dr. Peter Drucker'),
('College of Education', 'Dr. Maria Montessori');
