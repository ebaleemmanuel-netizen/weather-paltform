-- Demo data: 3 farmers, 1 association, 6 farms, 10 plantings, 8 yield reports, admin + ministry users
USE uganda_agri_weather;

-- Admin & ministry users (password: admin123 / password123)
-- Hashes generated with password_hash('admin123', PASSWORD_DEFAULT) and password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (full_name, email, password_hash, role_id, district) VALUES
('System Admin', 'admin@agriweather.ug', '$2y$10$vgoRHYsWNtu83PolmL3Nn.XviP.ZIoP5Bgps5cMQjVtJ1KomFkZ2q', (SELECT id FROM roles WHERE name='admin'), 'Kampala'),
('Ministry Officer', 'ministry@demo.ug', '$2y$10$s.8CIn1B/0n7TUz8qFC27.G0MiMvka6YWiy0mB1O4hFnKMAd8jcNm', (SELECT id FROM roles WHERE name='ministry'), 'Kampala'),
('Grace Nakato', 'farmer@demo.ug', '$2y$10$s.8CIn1B/0n7TUz8qFC27.G0MiMvka6YWiy0mB1O4hFnKMAd8jcNm', (SELECT id FROM roles WHERE name='farmer'), 'Mukono'),
('Peter Okello', 'peter.okello@demo.ug', '$2y$10$s.8CIn1B/0n7TUz8qFC27.G0MiMvka6YWiy0mB1O4hFnKMAd8jcNm', (SELECT id FROM roles WHERE name='farmer'), 'Gulu'),
('Sarah Nabirye', 'sarah.nabirye@demo.ug', '$2y$10$s.8CIn1B/0n7TUz8qFC27.G0MiMvka6YWiy0mB1O4hFnKMAd8jcNm', (SELECT id FROM roles WHERE name='association_admin'), 'Mbale'),
('James Tumusiime', 'james.t@demo.ug', '$2y$10$s.8CIn1B/0n7TUz8qFC27.G0MiMvka6YWiy0mB1O4hFnKMAd8jcNm', (SELECT id FROM roles WHERE name='extension_officer'), 'Mbarara'),
('Dr. Aisha Namono', 'aisha.namono@demo.ug', '$2y$10$s.8CIn1B/0n7TUz8qFC27.G0MiMvka6YWiy0mB1O4hFnKMAd8jcNm', (SELECT id FROM roles WHERE name='researcher'), 'Kampala');

-- Association
INSERT INTO associations (name, district, admin_user_id) VALUES
('Mbale Farmers Cooperative', 'Mbale', (SELECT id FROM users WHERE email='sarah.nabirye@demo.ug'));

-- Farms (6)
INSERT INTO farms (user_id, name, district, sub_county, latitude, longitude, size_acres, soil_type, irrigation_type) VALUES
((SELECT id FROM users WHERE email='farmer@demo.ug'), 'Nakato Homestead Farm', 'Mukono', 'Goma', 0.35, 32.75, 3.5, 'Loam', 'Rain-fed'),
((SELECT id FROM users WHERE email='farmer@demo.ug'), 'Riverside Plot', 'Mukono', 'Nama', 0.34, 32.76, 1.2, 'Clay loam', 'Drip irrigation'),
((SELECT id FROM users WHERE email='peter.okello@demo.ug'), 'Okello Family Farm', 'Gulu', 'Bungatira', 2.77, 32.30, 5.0, 'Sandy loam', 'Rain-fed'),
((SELECT id FROM users WHERE email='sarah.nabirye@demo.ug'), 'Cooperative Demo Plot', 'Mbale', 'Wanale', 1.08, 34.18, 2.0, 'Volcanic soil', 'Sprinkler'),
((SELECT id FROM users WHERE email='james.t@demo.ug'), 'Extension Trial Farm', 'Mbarara', 'Rwanyamahembe', -0.60, 30.65, 4.0, 'Clay loam', 'Rain-fed'),
((SELECT id FROM users WHERE email='peter.okello@demo.ug'), 'North Field', 'Gulu', 'Paicho', 2.78, 32.31, 2.5, 'Sandy loam', 'None');

-- Plantings (10, mix of harvested/growing)
INSERT INTO plantings (farm_id, crop_id, planting_date, transplant_date, expected_harvest_date, status) VALUES
(1, (SELECT id FROM crops WHERE name='Maize'), '2025-03-15', NULL, '2025-07-15', 'harvested'),
(1, (SELECT id FROM crops WHERE name='Beans'), '2025-08-10', NULL, '2025-11-10', 'growing'),
(2, (SELECT id FROM crops WHERE name='Bananas/Matooke'), '2024-06-01', '2024-10-01', '2025-06-01', 'harvested'),
(3, (SELECT id FROM crops WHERE name='Groundnuts'), '2025-03-20', NULL, '2025-06-28', 'harvested'),
(3, (SELECT id FROM crops WHERE name='Cassava'), '2025-04-01', NULL, '2025-12-27', 'growing'),
(4, (SELECT id FROM crops WHERE name='Coffee (Robusta)'), '2023-05-01', '2023-11-01', '2025-05-01', 'harvested'),
(4, (SELECT id FROM crops WHERE name='Maize'), '2025-08-20', NULL, '2025-12-20', 'growing'),
(5, (SELECT id FROM crops WHERE name='Rice (Upland)'), '2025-03-10', NULL, '2025-08-07', 'harvested'),
(6, (SELECT id FROM crops WHERE name='Sweet Potato'), '2025-03-25', NULL, '2025-07-23', 'harvested'),
(6, (SELECT id FROM crops WHERE name='Groundnuts'), '2025-08-15', NULL, '2025-11-23', 'growing');

-- Yield reports (8, mix of good/bad)
INSERT INTO yield_reports (planting_id, outcome, comment) VALUES
(1, 'good', 'Strong harvest, rains were reliable this season.'),
(3, 'good', 'Bananas did very well with the irrigation.'),
(4, 'bad', 'Termite damage reduced the yield significantly.'),
(6, 'good', 'Coffee cherries were healthy, good market price too.'),
(8, 'bad', 'Too much rain caused waterlogging and rice blast.'),
(9, 'good', 'Sweet potatoes did well, applied fertilizer on schedule.');

-- 6 reports only (2 harvested plantings left unreported intentionally to demo the "yield.php" flow)

INSERT INTO yield_report_reasons (yield_report_id, yield_reason_id) VALUES
(1, (SELECT id FROM yield_reasons WHERE label='Good weather')),
(1, (SELECT id FROM yield_reasons WHERE label='Timely planting')),
(2, (SELECT id FROM yield_reasons WHERE label='Good weather')),
(2, (SELECT id FROM yield_reasons WHERE label='Fertilizer applied')),
(3, (SELECT id FROM yield_reasons WHERE label='Pest')),
(4, (SELECT id FROM yield_reasons WHERE label='Good seed')),
(4, (SELECT id FROM yield_reasons WHERE label='Proper pest control')),
(5, (SELECT id FROM yield_reasons WHERE label='Too much rain')),
(5, (SELECT id FROM yield_reasons WHERE label='Disease')),
(6, (SELECT id FROM yield_reasons WHERE label='Fertilizer applied')),
(6, (SELECT id FROM yield_reasons WHERE label='Timely planting'));

-- Announcements
INSERT INTO announcements (title, body, created_by) VALUES
('Welcome to the pilot', 'The Uganda Agri-Weather Platform is now live for the 12-district pilot. Farmers can register and start logging farms today.', (SELECT id FROM users WHERE email='admin@agriweather.ug')),
('Rainy season advisory', 'Extension officers should encourage early planting of maize and beans ahead of the March rains.', (SELECT id FROM users WHERE email='ministry@demo.ug'));
