SET NAMES utf8mb4;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE motorcycles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(100) NOT NULL,
    plate VARCHAR(20),
    color VARCHAR(30),
    capacity INT,
    description TEXT,
    status ENUM('active','inactive','maintenance','sold') DEFAULT 'active',
    price_per_hour INT NOT NULL,
    price_half_day INT NOT NULL,
    price_per_day INT NOT NULL,
    price_per_week INT NOT NULL,
    price_per_month INT NOT NULL,
    insurance VARCHAR(100),
    year INT,
    mileage INT,
    available TINYINT(1) DEFAULT 1
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE motorcycle_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorcycle_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    motorcycle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    amount INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO admins (name, email, password) VALUES
('مدیر', 'admin@example.com', '0192023a7bbd73250516f069df18b500');

INSERT INTO users (name, email, password) VALUES
('کاربر نمونه', 'user@example.com', '6ad14ba9986e3615423dfca256d04e3f');

INSERT INTO motorcycles (model, plate, color, capacity, description, status, price_per_hour, price_half_day, price_per_day, price_per_week, price_per_month, insurance, year, mileage, available) VALUES
('اسکوتر وسپا', '11ک123-45', 'قرمز', 150, 'اسکوتر شهری', 'active', 30000, 70000, 120000, 750000, 2800000, 'بیمه شخص ثالث', 2022, 5000, 1),
('موتور کروزر', '22د456-78', 'مشکی', 250, 'کروز با قدرت بالا', 'maintenance', 50000, 110000, 180000, 1100000, 4000000, 'بیمه کامل', 2021, 12000, 0),
('موتور برقی', '33ه789-01', 'سفید', 100, 'برقی کم صدا', 'active', 20000, 50000, 90000, 550000, 2000000, 'بیمه پایه', 2023, 2000, 1);
