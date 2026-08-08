CREATE DATABASE IF NOT EXISTS ems_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ems_db;

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    gender VARCHAR(10),
    department_id INT,
    salary DECIMAL(10,2) DEFAULT 0,
    joining_date DATE,
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
    employee_id INT DEFAULT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    UNIQUE KEY unique_employee_date (employee_id, attendance_date),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE TABLE leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

INSERT INTO departments (department_name) VALUES
('Human Resources'),
('Engineering'),
('Sales'),
('Marketing'),
('Finance');

INSERT INTO employees
(employee_code, name, email, phone, gender, department_id, salary, joining_date)
VALUES
('EMP001', 'Asha Mehta', 'asha.mehta@example.com', '9876543210', 'Female', 2, 65000.00, '2023-01-15'),
('EMP002', 'Rahul Verma', 'rahul.verma@example.com', '9876543211', 'Male', 3, 48000.00, '2023-03-10'),
('EMP003', 'Priya Nair', 'priya.nair@example.com', '9876543212', 'Female', 1, 42000.00, '2022-11-01');

INSERT INTO users (name, email, password, role, employee_id)
VALUES
('System Admin', 'admin@ems.com',
 '$2y$12$TNqDYt2iIBo8F2dQLsLTseA0R.Fs1Cqq9weK7KuOVwdKFdp7IT4ey',
 'admin', NULL);

INSERT INTO users (name, email, password, role, employee_id)
VALUES
('Asha Mehta', 'asha.mehta@example.com',
 '$2y$12$b2/3fNhhDy1byOo7/q3nw.IwzPQsF1aQ5qhvKNsS/Vn9QpXAzqGZW',
 'employee', 1),
('Rahul Verma', 'rahul.verma@example.com',
 '$2y$12$b2/3fNhhDy1byOo7/q3nw.IwzPQsF1aQ5qhvKNsS/Vn9QpXAzqGZW',
 'employee', 2);

INSERT INTO attendance (employee_id, attendance_date, status)
VALUES
(1, CURDATE(), 'Present'),
(2, CURDATE(), 'Present'),
(3, CURDATE(), 'Absent');

INSERT INTO leaves
(employee_id, leave_type, start_date, end_date, reason, status)
VALUES
(3, 'Sick Leave',
 DATE_ADD(CURDATE(), INTERVAL 2 DAY),
 DATE_ADD(CURDATE(), INTERVAL 3 DAY),
 'Fever and cold, need rest.',
 'Pending');
