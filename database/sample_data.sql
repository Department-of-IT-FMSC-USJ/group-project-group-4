INSERT INTO fines (vehicle_number, license_number, place, driver_name, driver_address, mistake_id) VALUES
('CAR-1234', 'DL123456', 'Colombo Fort', 'John Perera', '123 Main Street, Colombo 01', 1),
('BUS-5678', 'DL789012', 'Kandy City', 'Jane Silva', '456 Hill Road, Kandy', 10),
('VAN-9999', 'DL555666', 'Galle Road', 'Mike Fernando', '789 Beach Road, Mount Lavinia', 3),
('CAR-2468', 'DL987654', 'Negombo', 'Sarah Wijesinghe', '321 Church Street, Negombo', 19),
('BIKE-1357', 'DL246810', 'Maharagama', 'David Rajapaksa', '654 Temple Road, Maharagama', 2);


INSERT INTO payments (amount, status) VALUES
(1000.00, 'Completed'),
(3000.00, 'Pending'),
(2000.00, 'Completed'),
(500.00, 'Failed'),
(1500.00, 'Pending'),
(200.00, 'Completed');


INSERT INTO birth_certificates (birth_certificate_number, date_of_birth, place_of_birth, father_date_of_birth, father_place_of_birth, mother_date_of_birth, mother_place_of_birth, payment_id) VALUES
('BC001', '2000-01-15', 'Colombo General Hospital', '1975-05-20', 'Kandy General Hospital', '1978-08-10', 'Matara Hospital', 1),
('BC002', '1995-06-20', 'Kandy Teaching Hospital', '1970-03-15', 'Colombo General Hospital', '1972-11-25', 'Galle Hospital', 6),
('BC003', '2005-12-25', 'Castle Street Hospital', '1980-07-30', 'Anuradhapura Hospital', '1985-04-18', 'Kurunegala Hospital', NULL);


INSERT INTO identity_card_applications (
    district, divisional_secretariat_division, grama_niladari_number_and_division,
    family_name, name_, surname, sex, civil_status, profession, date_of_birth,
    birth_certificate_no, place_of_birth, birth_division, birth_district,
    perm_house_name_no, perm_building_type, perm_road_street, perm_village_city, perm_postal_code,
    postal_house_name_no, postal_building_type, postal_road_street, postal_village_city, postal_postal_code,
    phone_mobile, email, application_purpose, birth_certificate_pdf, photo_pdf, photo_link, payment_id
) VALUES
(
    'Colombo', 'Colombo', 'GN001/A',
    'Perera', 'Kasun', 'Chamara', 'Male', 'Single', 'Software Engineer', '1990-01-15',
    'BC001', 'Colombo', 'Colombo', 'Colombo',
    '123/A', 'House', 'Main Road', 'Colombo 03', '00300',
    '123/A', 'House', 'Main Road', 'Colombo 03', '00300',
    '0771234567', 'kasun@email.com', 'Lost', 'dummy_pdf_data', 'dummy_photo_data', 'photo1.jpg', 1
),
(
    'Kandy', 'Kandy', 'GN002/B',
    'Silva', 'Nimali', 'Kumari', 'Female', 'Married', 'Teacher', '1985-07-20',
    'BC002', 'Kandy', 'Kandy', 'Kandy',
    '456/B', 'Apartment', 'Temple Road', 'Kandy', '20000',
    '456/B', 'Apartment', 'Temple Road', 'Kandy', '20000',
    '0712345678', 'nimali@email.com', 'Renew', 'dummy_pdf_data', 'dummy_photo_data', 'photo2.jpg', NULL
),
(
    'Galle', 'Galle', 'GN003/C',
    'Fernando', 'Ruwan', 'Priyantha', 'Male', 'Single', 'Doctor', '1988-03-10',
    'BC003', 'Galle', 'Galle', 'Galle',
    '789/C', 'House', 'Beach Road', 'Galle', '80000',
    '789/C', 'House', 'Beach Road', 'Galle', '80000',
    '0723456789', 'ruwan@email.com', 'Changes', 'dummy_pdf_data', 'dummy_photo_data', 'photo3.jpg', 3
);


UPDATE fines SET payment_id = 1 WHERE fine_id = 1;
UPDATE fines SET payment_id = 2 WHERE fine_id = 2;
UPDATE fines SET payment_id = 3 WHERE fine_id = 3;


INSERT INTO payments (amount, status) VALUES
(750.00, 'Completed'),   -- For NIC application fee
(300.00, 'Pending'),     -- For birth certificate fee
(1200.00, 'Completed'),  -- For multiple fines
(450.00, 'Failed');      -- For failed payment

