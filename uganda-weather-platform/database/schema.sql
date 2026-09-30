-- Uganda Agricultural Weather Platform - Database Schema
-- Dropping first makes this script safe to re-run: it always starts from a
-- clean, correctly-structured database instead of leaving stale tables
-- behind from an earlier partial/failed import.
DROP DATABASE IF EXISTS uganda_agri_weather;
CREATE DATABASE uganda_agri_weather CHARACTER SET utf8mb4;
USE uganda_agri_weather;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE regions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE districts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  region_id INT,
  latitude DECIMAL(9,6),
  longitude DECIMAL(9,6),
  FOREIGN KEY (region_id) REFERENCES regions(id)
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  district VARCHAR(100),
  phone VARCHAR(30),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE associations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  district VARCHAR(100),
  admin_user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_user_id) REFERENCES users(id)
);

CREATE TABLE association_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  association_id INT NOT NULL,
  user_id INT NOT NULL,
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (association_id) REFERENCES associations(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE farms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  district VARCHAR(100),
  sub_county VARCHAR(100),
  latitude DECIMAL(9,6),
  longitude DECIMAL(9,6),
  size_acres DECIMAL(8,2),
  soil_type VARCHAR(50),
  irrigation_type VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE crops (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  scientific_name VARCHAR(150),
  category ENUM('food','cash','both') DEFAULT 'food',
  min_rain_mm INT, max_rain_mm INT,
  min_temp_c DECIMAL(4,1), max_temp_c DECIMAL(4,1),
  preferred_soil VARCHAR(150),
  planting_season VARCHAR(100),
  planting_window VARCHAR(100),
  days_to_maturity INT,
  best_transplant_time VARCHAR(150),
  best_watering_time VARCHAR(150),
  common_pests TEXT,
  common_diseases TEXT,
  notes TEXT,
  status ENUM('active','pending','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE crop_audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  crop_id INT NOT NULL,
  user_id INT,
  action VARCHAR(50),
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (crop_id) REFERENCES crops(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE plantings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  farm_id INT NOT NULL,
  crop_id INT NOT NULL,
  planting_date DATE,
  transplant_date DATE,
  expected_harvest_date DATE,
  status ENUM('growing','harvested','failed') DEFAULT 'growing',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (farm_id) REFERENCES farms(id),
  FOREIGN KEY (crop_id) REFERENCES crops(id)
);

CREATE TABLE yield_reasons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(150) NOT NULL,
  outcome ENUM('good','bad') NOT NULL
);

CREATE TABLE yield_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  planting_id INT NOT NULL,
  outcome ENUM('good','bad') NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (planting_id) REFERENCES plantings(id)
);

CREATE TABLE yield_report_reasons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  yield_report_id INT NOT NULL,
  yield_reason_id INT NOT NULL,
  FOREIGN KEY (yield_report_id) REFERENCES yield_reports(id),
  FOREIGN KEY (yield_reason_id) REFERENCES yield_reasons(id)
);

CREATE TABLE weather_cache (
  id INT AUTO_INCREMENT PRIMARY KEY,
  district_id INT NOT NULL,
  temperature_c DECIMAL(4,1),
  rain_mm DECIMAL(6,2),
  weather_code INT,
  fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (district_id) REFERENCES districts(id)
);

CREATE TABLE announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  body TEXT,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(100),
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Seed roles
INSERT INTO roles (name) VALUES ('farmer'),('association_admin'),('extension_officer'),('researcher'),('ministry'),('admin');

-- Seed regions
INSERT INTO regions (name) VALUES ('Central'),('Eastern'),('Northern'),('Western');

-- Seed 12 districts with approx coordinates
INSERT INTO districts (name, region_id, latitude, longitude) VALUES
('Kampala', 1, 0.3476, 32.5825),
('Wakiso', 1, 0.4044, 32.4594),
('Mukono', 1, 0.3533, 32.7553),
('Jinja', 2, 0.4478, 33.2026),
('Mbale', 2, 1.0827, 34.1755),
('Soroti', 2, 1.7147, 33.6111),
('Gulu', 3, 2.7747, 32.2990),
('Lira', 3, 2.2350, 32.9096),
('Arua', 3, 3.0201, 30.9111),
('Mbarara', 4, -0.6072, 30.6545),
('Kabale', 4, -1.2491, 29.9897),
('Fort Portal', 4, 0.6710, 30.2749);

-- Seed 10 crops
INSERT INTO crops (name, scientific_name, category, min_rain_mm, max_rain_mm, min_temp_c, max_temp_c, preferred_soil, planting_season, planting_window, days_to_maturity, best_transplant_time, best_watering_time, common_pests, common_diseases, status) VALUES
('Maize','Zea mays','food',500,1200,18,32,'Loam, Sandy loam','March-May, Aug-Sep','Early rains',120,'2-3 weeks after sowing','Early morning','Fall armyworm, Stalk borer','Maize streak virus, Rust','active'),
('Beans','Phaseolus vulgaris','food',400,900,16,28,'Loam, Clay loam','March-May, Aug-Sep','Early rains',90,'N/A (direct seeded)','Early morning','Bean fly, Aphids','Bean rust, Anthracnose','active'),
('Coffee (Robusta)','Coffea canephora','cash',1000,2000,18,28,'Volcanic loam, Clay loam','Year-round (Mar-May best)','With first rains',730,'6-8 months in nursery','Early morning','Coffee berry borer','Coffee wilt disease','active'),
('Bananas/Matooke','Musa spp.','food',1000,2500,15,30,'Loam, Volcanic soil','Year-round','With rains',365,'3-5 months in nursery','Morning or evening','Banana weevil','Bacterial wilt (BXW)','active'),
('Cassava','Manihot esculenta','food',600,1500,20,32,'Sandy loam, Loam','March-May','Any rainy season',270,'N/A (stem cuttings)','Not critical','Cassava mealybug','Cassava mosaic virus','active'),
('Groundnuts','Arachis hypogaea','food',500,1000,20,30,'Sandy loam','March-May, Aug-Sep','Early rains',100,'N/A (direct seeded)','Early morning','Aphids, Termites','Groundnut rosette','active'),
('Sweet Potato','Ipomoea batatas','food',500,1200,18,30,'Sandy loam, Loam','March-May, Aug-Sep','Early rains',120,'Vine cuttings at planting','Morning or evening','Sweet potato weevil','Sweet potato virus disease','active'),
('Rice (Upland)','Oryza sativa','food',1000,2000,20,32,'Clay, Clay loam','March-May','Early rains',150,'N/A (direct seeded/nursery)','Consistent moisture','Rice stem borer','Rice blast','active'),
('Cotton','Gossypium spp.','cash',700,1300,20,32,'Sandy loam, Clay loam','March-May','Early rains',180,'N/A (direct seeded)','Early rains critical','Bollworm','Bacterial blight','active'),
('Tea','Camellia sinensis','cash',1200,2500,15,25,'Volcanic loam, Acidic soils','Year-round','With rains',730,'12-18 months in nursery','Consistent moisture','Tea mosquito bug','Blister blight','active');

-- Seed yield reasons
INSERT INTO yield_reasons (label, outcome) VALUES
('Good weather','good'),('Fertilizer applied','good'),('Good seed','good'),('Proper pest control','good'),('Timely planting','good'),
('Bad weather','bad'),('Too much rain','bad'),('Pest','bad'),('Disease','bad'),('No fertilizer','bad'),('Late planting','bad'),('Poor seed','bad');
