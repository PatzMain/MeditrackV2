CREATE DATABASE meditrack_system;
USE meditrack_system;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'superadmin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms Table (Fixed University Clinic Rooms)
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name ENUM('Treatment Room', 'Records Room', 'Dental Room', 'Medical Ward Room') NOT NULL,
    total_beds INT DEFAULT 0
);

-- Beds Table (Only for Medical Ward)
CREATE TABLE beds (
    bed_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    bed_number VARCHAR(10) NOT NULL,
    bed_status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- Medical Medicines Table
CREATE TABLE medical_medicines (
    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_name VARCHAR(100) NOT NULL,
    medicine_dosage VARCHAR(50),
    medicine_unit VARCHAR(50),
    medicine_stock INT DEFAULT 0,
    medicine_expiry_date DATE,
    lot_batch_no VARCHAR(50),
    delivery_date DATE,
    outgoing_date DATE,
    items_delivered INT,
    items_taken INT,
    remaining_balance INT,
    medicine_classification ENUM(
        'Antibiotic', 'Analgesic', 'Antipyretic', 'Antihistamine', 'Antiseptic',
        'Antifungal', 'Antiviral', 'Vaccine', 'Supplement', 'Cough Suppressant',
        'Decongestant', 'Anti-inflammatory', 'Antacid', 'Laxative', 'Other'
    ),
    medicine_brand_name VARCHAR(100),
    medicine_generic_name VARCHAR(100),
    medicine_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dental Medicines Table
CREATE TABLE dental_medicines (
    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_name VARCHAR(100) NOT NULL,
    medicine_dosage VARCHAR(50),
    medicine_unit VARCHAR(50),
    medicine_stock INT DEFAULT 0,
    medicine_expiry_date DATE,
    lot_batch_no VARCHAR(50),
    delivery_date DATE,
    outgoing_date DATE,
    items_delivered INT,
    items_taken INT,
    remaining_balance INT,
    medicine_classification ENUM(
        'Fluoride Treatment', 'Local Anesthetic', 'Desensitizing Agent',
        'Dental Antibiotic', 'Mouth Rinse', 'Topical Analgesic', 'Oral Antifungal',
        'Gingivitis Treatment', 'Periodontitis Treatment', 'Dry Mouth Treatment', 'Other'
    ),
    medicine_brand_name VARCHAR(100),
    medicine_generic_name VARCHAR(100),
    medicine_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical Supplies Table
CREATE TABLE medical_supplies (
    supply_id INT AUTO_INCREMENT PRIMARY KEY,
    supply_name VARCHAR(100) NOT NULL,
    supply_quantity INT DEFAULT 0,
    supply_unit VARCHAR(50),
    supply_expiry_date DATE,
    lot_batch_no VARCHAR(50),
    delivery_date DATE,
    outgoing_date DATE,
    items_delivered INT,
    items_taken INT,
    remaining_balance INT,
    supply_classification ENUM(
        'Syringe', 'Gloves', 'Bandage', 'Cotton', 'Alcohol Swab',
        'Face Mask', 'IV Set', 'Thermometer Cover', 'Disinfectant', 'Protective Gown', 'Other'
    ),
    supply_brand_name VARCHAR(100),
    supply_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dental Supplies Table
CREATE TABLE dental_supplies (
    supply_id INT AUTO_INCREMENT PRIMARY KEY,
    supply_name VARCHAR(100) NOT NULL,
    supply_quantity INT DEFAULT 0,
    supply_unit VARCHAR(50),
    supply_expiry_date DATE,
    lot_batch_no VARCHAR(50),
    delivery_date DATE,
    outgoing_date DATE,
    items_delivered INT,
    items_taken INT,
    remaining_balance INT,
    supply_classification ENUM(
        'Dental Bib', 'Cotton Roll', 'Dental Floss', 'Dental Impression Material',
        'Saliva Ejector', 'Dental Tray Cover', 'Disinfectant', 'Protective Gown', 'Other'
    ),
    supply_brand_name VARCHAR(100),
    supply_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical Equipment Table
CREATE TABLE medical_equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_name VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100),
    equipment_condition VARCHAR(50),
    remarks TEXT,
    equipment_location VARCHAR(100),
    equipment_classification ENUM(
        'Stethoscope', 'Blood Pressure Monitor', 'Otoscope', 'Weighing Scale',
        'Nebulizer', 'Sterilizer/Autoclave', 'Examination Light', 'Other'
    ),
    equipment_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dental Equipment Table
CREATE TABLE dental_equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_name VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100),
    equipment_condition VARCHAR(50),
    remarks TEXT,
    equipment_location VARCHAR(100),
    equipment_classification ENUM(
        'Dental Chair', 'Dental Drill', 'Ultrasonic Scaler', 'Curing Light',
        'X-ray Machine', 'Sterilizer/Autoclave', 'Examination Light', 'Other'
    ),
    equipment_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients Table with University-specific fields
CREATE TABLE patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_number VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('M', 'F', 'Other') NOT NULL,
    blood_group VARCHAR(5),
    phone VARCHAR(20),
    email VARCHAR(100),
    student_type ENUM('College', 'LSHS') NOT NULL,  -- Added
    course VARCHAR(100),                           -- For College
    year_level INT,                                -- For College
    grade_level INT,                               -- For LSHS
    strand VARCHAR(100),                           -- For LSHS
    patient_address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    allergies TEXT,
    medical_conditions TEXT,
    admission_date DATE,
    discharge_date DATE,
    patient_status ENUM('admitted', 'discharged', 'deceased', 'transferred') DEFAULT 'admitted',
    room_id INT,
    bed_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE SET NULL,
    FOREIGN KEY (bed_id) REFERENCES beds(bed_id) ON DELETE SET NULL
);

-- Vital Signs Table
CREATE TABLE vital_signs (
    vital_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    systolic_bp INT,
    diastolic_bp INT,
    heart_rate INT,
    respiratory_rate INT,
    temperature DECIMAL(4,2),
    temperature_unit ENUM('C', 'F') DEFAULT 'C',
    oxygen_saturation INT,
    blood_glucose DECIMAL(5,2),
    v_weight DECIMAL(5,2),
    v_height DECIMAL(5,2),
    pain_scale INT CHECK (pain_scale BETWEEN 0 AND 10),
    consciousness_level ENUM('alert', 'drowsy', 'unconscious', 'confused'),
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Medical Assessments Table
CREATE TABLE medical_assessments (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    assessment_type ENUM('initial', 'daily_round', 'consultation', 'discharge', 'emergency') NOT NULL,
    chief_complaint TEXT,
    present_illness TEXT,
    physical_examination TEXT,
    diagnosis TEXT,
    treatment_plan TEXT,
    prognosis TEXT,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_date DATE,
    priority_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    assessment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Nursing Notes Table
CREATE TABLE nursing_notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    nursing_shift ENUM('morning', 'afternoon', 'night') NOT NULL,
    nursing_note_type ENUM('assessment', 'care_plan', 'medication', 'incident', 'discharge_prep', 'general') NOT NULL,
    nursing_note_content TEXT NOT NULL,
    nursing_action_taken TEXT,
    nursing_follow_up_needed BOOLEAN DEFAULT FALSE,
    nursing_priority ENUM('low', 'medium', 'high') DEFAULT 'low',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Activity Logs Table
CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    patient_id INT NULL,
    logs_item_type ENUM(
        'medical_medicine', 'dental_medicine', 
        'medical_supply', 'dental_supply', 
        'medical_equipment', 'dental_equipment',
        'authentication', 'system', 'patient', 
        'vital_signs', 'medication_admin', 'lab_test', 'assessment'
    ) NULL,
    logs_item_id INT NULL,
    logs_item_name VARCHAR(100) NULL,
    logs_description TEXT NOT NULL,
    logs_reason VARCHAR(255) NULL,
    logs_quantity INT NULL,
    logs_status VARCHAR(50) NULL,
    logs_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE SET NULL
);

-- Insert predefined rooms
INSERT INTO rooms (room_name, total_beds) VALUES
('Treatment Room', 0),
('Records Room', 0),
('Dental Room', 0),
('Medical Ward Room', 4);

-- Insert Medical Ward Beds
INSERT INTO beds (room_id, bed_number) VALUES
(4, 'Bed 1'),
(4, 'Bed 2'),
(4, 'Bed 3'),
(4, 'Bed 4');
