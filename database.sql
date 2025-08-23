CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user'
);

CREATE TABLE motorcycles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price_per_day INT NOT NULL
);

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
);

INSERT INTO users (name, email, password, role) VALUES
('مدیر', 'admin@example.com', '$2y$12$7FbEGOv5QPEi7n5XK4KnfuFXmOucpGfVUvdzlx08JWvvY9/Qp/3Ry', 'admin'),
('کاربر نمونه', 'user@example.com', '$2y$12$ii7WwZtQ/FQxd7bo85iEk.3qAI/1b0bJyLfVO19fdKjNTRxywzEQW', 'user');

INSERT INTO motorcycles (name, price_per_day) VALUES
('اسکوتر وسپا', 120000),
('موتور کروزر', 180000),
('موتور برقی', 90000);
