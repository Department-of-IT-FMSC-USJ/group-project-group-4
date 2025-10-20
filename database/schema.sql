CREATE DATABASE IF NOT EXISTS OneID;
USE OneID;

-- Payment table Nayanthi
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Completed', 'Failed', 'Cancelled') DEFAULT 'Pending'
);

-- Mistake table Harsha
CREATE TABLE mistakes (
    mistake_id INT AUTO_INCREMENT PRIMARY KEY,
    mistake VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL
);

-- Fine table Tharuka
CREATE TABLE fines (
    fine_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(15) NOT NULL,
    license_number VARCHAR(15) NOT NULL,
    place VARCHAR(100) NOT NULL,
    driver_name VARCHAR(100) NOT NULL,
    driver_address VARCHAR(255) NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_at TIMESTAMP NULL,
    processed_by_admin BOOLEAN DEFAULT FALSE,
    payment_id INT NULL,
    mistake_id INT NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id),
    FOREIGN KEY (mistake_id) REFERENCES mistakes(mistake_id)
);

-- Birth Certificates table Dushan
CREATE TABLE birth_certificates (
    birth_certificate_number VARCHAR(50) PRIMARY KEY,
    date_of_birth DATE NOT NULL,
    place_of_birth VARCHAR(100) NOT NULL,
    father_date_of_birth DATE,
    father_place_of_birth VARCHAR(100),
    mother_date_of_birth DATE,
    mother_place_of_birth VARCHAR(100),
    processed_by_admin BOOLEAN DEFAULT FALSE,
    payment_id INT NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

-- Identity Card Applications table Inshab
CREATE TABLE identity_card_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    district VARCHAR(100) NOT NULL,
    divisional_secretariat_division VARCHAR(100) NOT NULL,
    grama_niladari_number_and_division VARCHAR(100) NOT NULL,
    family_name VARCHAR(100) NOT NULL,
    name_ VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    id_card_family_name VARCHAR(100),
    id_card_name VARCHAR(100),
    id_card_surname VARCHAR(100),
    sex ENUM('Male', 'Female') NOT NULL,
    civil_status ENUM('Married', 'Single', 'Widowed', 'Divorced') NOT NULL,
    profession VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    birth_certificate_no VARCHAR(50) NOT NULL,
    place_of_birth VARCHAR(100) NOT NULL,
    birth_division VARCHAR(100) NOT NULL,
    birth_district VARCHAR(100) NOT NULL,
    country_of_birth VARCHAR(100),
    city_of_birth VARCHAR(100),
    citizenship_cert_no VARCHAR(50),
    perm_house_name_no VARCHAR(100) NOT NULL,
    perm_building_type VARCHAR(100) NOT NULL,
    perm_road_street VARCHAR(100) NOT NULL,
    perm_village_city VARCHAR(100) NOT NULL,
    perm_postal_code VARCHAR(10) NOT NULL,
    postal_house_name_no VARCHAR(100) NOT NULL,
    postal_building_type VARCHAR(100) NOT NULL,
    postal_road_street VARCHAR(100) NOT NULL,
    postal_village_city VARCHAR(100) NOT NULL,
    postal_postal_code VARCHAR(10) NOT NULL,
    phone_residence VARCHAR(20),
    phone_mobile VARCHAR(20),
    email VARCHAR(100),
    application_purpose ENUM('Lost', 'Changes', 'Renew', 'Damaged') NOT NULL,
    lost_id_card_no VARCHAR(20),
    id_card_issue_date DATE,
    police_station_name VARCHAR(100),
    police_report_date DATE,
    birth_certificate_pdf LONGBLOB NOT NULL,
    police_report_doc LONGBLOB NOT NULL,
    photo_link VARCHAR(255) NOT NULL,
    photo_pdf LONGBLOB,
    processed_by_admin BOOLEAN DEFAULT FALSE,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_id INT NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

-- Admins table Harsha
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);


-- Insert traffic violation mistakes
INSERT INTO mistakes (mistake, amount) VALUES
('Not displaying identification plates', 1000),
('Not wearing helmets', 1000),
('Distribution of Advertisements', 1000),
('Driving Emergency Service Vehicles & Public Service Vehicles without DL', 1000),
('Vehicles without license purpose', 1000),
('Carrying fuel into tank', 1000),
('Driving a vehicle loaded with chemicals hazardous waste without license', 1000),
('Not having license to drive a specific class of vehicles', 1000),
('Not carrying DL', 1000),
('Contravening Speed Limits', 3000),
('Disobeying Road Rules', 2000),
('Obstructing control of the motor vehicle', 1000),
('Signals by Driver', 1000),
('Reversing for a long Distance', 1000),
('Sound or Light warnings', 1000),
('Excessive emission of smoke etc', 1000),
('Riding on running boards', 500),
('No. of persons in front seats', 1000),
('Non-use of seat belts', 1000),
('Driving without pollution certificate', 500),
('Not wearing protective nets', 1000),
('Excessive use of noise', 1000),
('Disobeying directions & Signals of Police Officers Traffic', 2000),
('Non Compliance with Traffic Signals', 1000),
('Failure to take precautions when discharging fuel into tank', 1000),
('Parking of vehicle', 1000),
('Non-use of precautions while parking', 2000),
('Driving without insurance', 500),
('Excess carriage of passengers', 500),
('Carriage on lorry or Motor Tricycle carriage of goods in excess', 500),
('No of persons carried in a lorry', 500),
('Violation of Regulations on motor vehicles', 1000),
('Failure to carry the Emission / Fitness Certificate', 500);
