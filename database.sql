-- Mağusa Taxi Website Database Setup
CREATE DATABASE IF NOT EXISTS magusa_taxi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE magusa_taxi;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Website content table
CREATE TABLE IF NOT EXISTS website_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Images table for slider
CREATE TABLE IF NOT EXISTS slider_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact information table
CREATE TABLE IF NOT EXISTS contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    whatsapp_number VARCHAR(20),
    phone_number VARCHAR(20),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@magusataxi.com');

-- Insert default content
INSERT INTO website_content (section_name, content) VALUES 
('hero_title', 'Mağusa Taxi - Güvenilir Ulaşım Hizmeti'),
('hero_subtitle', 'Kıbrıs''ta Yıllardır Güvenilir Hizmet'),
('blog_content', '<h2>Yıllardır Güvenilir Hizmet</h2><p>Mağusa Taxi olarak, Kıbrıs''ın güzellikleri arasında yıllardır güvenilir ve konforlu ulaşım hizmeti sunuyoruz. Deneyimli şoförlerimiz ve modern araç filomizle, havaalanından şehir merkezine, turistik gezilerden iş toplantılarına kadar her türlü ulaşım ihtiyacınızı karşılıyoruz.</p><p>Müşteri memnuniyeti bizim önceliğimizdir. 7/24 hizmet veren ekibimizle, her saatte güvenilir ulaşım çözümleri sunuyoruz. Temiz araçlarımız, güler yüzlü hizmetimiz ve uygun fiyatlarımızla tercih edilen taxi hizmeti olmaktan gurur duyuyoruz.</p>'),
('meta_description', 'Mağusa Taxi - Kıbrıs''ta güvenilir taxi hizmeti. 7/24 hizmet, deneyimli şoförler, temiz araçlar. Havaalanı transferi ve şehir içi ulaşım.'),
('meta_keywords', 'Mağusa taxi, Kıbrıs taxi, Famagusta taxi, havaalanı transfer, güvenilir taxi, 7/24 taxi');

-- Multilingual content rows (used by PHP: key + _tr/_en/_ru with fallbacks)
INSERT INTO website_content (section_name, content) VALUES
('hero_title_tr', 'Mağusa Taxi'),
('hero_subtitle_tr', 'Güvenilir Ulaşım Hizmeti'),
('blog_content_tr', '<h2>Yıllardır Güvenilir Hizmet</h2><p>Mağusa Taxi olarak, Kıbrıs''ın güzellikleri arasında yıllardır güvenilir ve konforlu ulaşım hizmeti sunuyoruz. Deneyimli şoförlerimiz ve modern araç filomizle, havaalanından şehir merkezine, turistik gezilerden iş toplantılarına kadar her türlü ulaşım ihtiyacınızı karşılıyoruz.</p><p>Müşteri memnuniyeti bizim önceliğimizdir. 7/24 hizmet veren ekibimizle, her saatte güvenilir ulaşım çözümleri sunuyoruz. Temiz araçlarımız, güler yüzlü hizmetimiz ve uygun fiyatlarımızla tercih edilen taxi hizmeti olmaktan gurur duyuyoruz.</p>'),
('meta_description_tr', 'Mağusa Taxi - Kıbrıs''ta güvenilir taxi hizmeti. 7/24 hizmet, deneyimli şoförler, temiz araçlar.'),
('meta_keywords_tr', 'Mağusa taxi, Kıbrıs taxi, Famagusta taxi, havaalanı transfer'),

('hero_title_en', 'Famagusta Taxi'),
('hero_subtitle_en', 'Reliable Transportation Service'),
('blog_content_en', '<h2>Trusted Service for Years</h2><p>As Famagusta Taxi, we have been providing reliable and comfortable transportation across Cyprus for years. With our experienced drivers and modern fleet, we cover everything from airport transfers to city rides, sightseeing, and business trips.</p><p>Customer satisfaction is our priority. Our team operates 24/7 to offer dependable solutions at any hour. We are proud to be the preferred taxi service with clean cars, friendly service, and fair prices.</p>'),
('meta_description_en', 'Famagusta Taxi - Reliable taxi service in Cyprus. 24/7 service, experienced drivers, clean vehicles.'),
('meta_keywords_en', 'Famagusta taxi, Cyprus taxi, airport transfer'),

('hero_title_ru', 'Фамагуста Такси'),
('hero_subtitle_ru', 'Надежный Транспортный Сервис'),
('blog_content_ru', '<h2>Надежный сервис много лет</h2><p>Мы, Фамагуста Такси, уже много лет предоставляем надежные и комфортные перевозки по всему Кипру. Наши опытные водители и современный автопарк помогут вам в трансферах из аэропорта, поездках по городу, туристических турах и деловых встречах.</p><p>Удовлетворенность клиентов — наш приоритет. Мы работаем 24/7, предлагая надежные решения в любое время. Чистые автомобили, дружелюбный сервис и честные цены — причины выбирать нас.</p>'),
('meta_description_ru', 'Фамагуста Такси - Надежный сервис такси на Кипре. Обслуживание 24/7, опытные водители, чистые автомобили.'),
('meta_keywords_ru', 'Фамагуста такси, Кипр такси, аэропорт трансфер');

-- Insert default contact info
INSERT INTO contact_info (whatsapp_number, phone_number) VALUES 
('+90533123456', '+90392123456');

-- Insert sample images (you'll need to upload actual images)
INSERT INTO slider_images (image_path, alt_text, display_order, is_active) VALUES 
('assets/images/taxi1.jpg', 'Mağusa Taxi Araç 1', 1, TRUE),
('assets/images/taxi2.jpg', 'Mağusa Taxi Araç 2', 2, TRUE),
('assets/images/taxi3.jpg', 'Mağusa Taxi Araç 3', 3, TRUE);
