SET NAMES utf8mb4;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super','support','accountant','mechanic') DEFAULT 'support'
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('regular','vip','blocked') DEFAULT 'regular',
    note TEXT,
    language ENUM('fa','en') DEFAULT 'fa',
    notify_email TINYINT(1) DEFAULT 1
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
    available TINYINT(1) DEFAULT 1,
    lat DECIMAL(10,8) DEFAULT NULL,
    lng DECIMAL(11,8) DEFAULT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE motorcycle_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorcycle_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percent','fixed') NOT NULL,
    value INT NOT NULL,
    start_date DATE,
    end_date DATE,
    usage_limit INT DEFAULT NULL,
    used INT DEFAULT 0,
    per_user_limit INT DEFAULT NULL,
    vip_only TINYINT(1) DEFAULT 0,
    motor_id INT DEFAULT NULL,
    FOREIGN KEY (motor_id) REFERENCES motorcycles(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE discount_usages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discount_id INT NOT NULL,
    user_id INT NOT NULL,
    used_count INT DEFAULT 0,
    UNIQUE(discount_id,user_id),
    FOREIGN KEY (discount_id) REFERENCES discounts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    motorcycle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending','confirmed','in_use','returned','cancelled') DEFAULT 'pending',
    discount_id INT DEFAULT NULL,
    amount INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_id) REFERENCES discounts(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    amount INT NOT NULL,
    method ENUM('online','cash','pos') NOT NULL,
    status ENUM('paid','pending') DEFAULT 'paid',
    paid_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    motorcycle_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorcycle_id INT NOT NULL,
    service_date DATE NOT NULL,
    mileage INT,
    notes TEXT,
    cost INT DEFAULT 0,
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    admin_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    ip VARCHAR(45) PRIMARY KEY,
    attempts INT NOT NULL DEFAULT 0,
    last_attempt DATETIME NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    category VARCHAR(50) DEFAULT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    response TEXT,
    status ENUM('open','answered','closed') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    responded_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value VARCHAR(255)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO admins (name, email, password, role) VALUES
('مدیر', 'admin@example.com', '$2y$12$IWOcs0d0Q.uPO4D6xmeK2ex3Tz7V1guW3Yf16Kk2Qsz5EFHnpp91C','super');

INSERT INTO users (name, phone, email, password, status, note) VALUES
('کاربر نمونه', '09120000000', 'user@example.com', '$2y$12$1tKh0T5SbKtQP3wy4nPfCO2lv9MNXbgnTUZeIMhLIcQhYH7MnUq86', 'regular', '');

INSERT INTO motorcycles (model, plate, color, capacity, description, status, price_per_hour, price_half_day, price_per_day, price_per_week, price_per_month, insurance, year, mileage, available) VALUES
('اسکوتر وسپا', '11ک123-45', 'قرمز', 150, 'اسکوتر شهری', 'active', 30000, 70000, 120000, 750000, 2800000, 'بیمه شخص ثالث', 2022, 5000, 1),
('موتور کروزر', '22د456-78', 'مشکی', 250, 'کروز با قدرت بالا', 'maintenance', 50000, 110000, 180000, 1100000, 4000000, 'بیمه کامل', 2021, 12000, 0),
('موتور برقی', '33ه789-01', 'سفید', 100, 'برقی کم صدا', 'active', 20000, 50000, 90000, 550000, 2000000, 'بیمه پایه', 2023, 2000, 1);

INSERT INTO discounts (code, type, value, start_date, end_date, usage_limit, per_user_limit, vip_only, motor_id) VALUES
('OFF10','percent',10,NULL,NULL,NULL,NULL,0,NULL);

INSERT INTO notifications (user_id, message, is_read) VALUES
(1,'یادآوری: موتورتان را فردا تحویل بگیرید.',0),
(1,'پرداخت شما با موفقیت ثبت شد.',0);
