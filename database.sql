-- Създаване на базата данни
CREATE DATABASE IF NOT EXISTS furniture_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE furniture_shop;

-- Таблица за потребители
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица за мебели
CREATE TABLE IF NOT EXISTS furniture (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица за поръчки
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица за артикули в поръчки
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    furniture_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (furniture_id) REFERENCES furniture(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Създаване на администраторски акаунт (парола: admin123)
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@furniture.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', 'admin');

-- Добавяне на 10 мебели
INSERT INTO furniture (name, description, category, price, image, stock) VALUES
('Модерен диван "Комфорт"', 'Удобен триместен диван с меки възглавници и модерен дизайн. Изработен от висококачествен плат и здрава дървена рамка.', 'Дивани', 1299.99, 'sofa1.jpg', 5),
('Трапезна маса "Елеганс"', 'Красива дървена трапезна маса за 6 души. Изработена от масив дъб с естествен финиш.', 'Маси', 899.50, 'table1.jpg', 8),
('Спалня "Луксор"', 'Комплект спалня включваща легло 160x200, 2 нощни шкафчета и гардероб. Модерен дизайн в тъмно кафяво.', 'Спални', 2499.00, 'bedroom1.jpg', 3),
('Офис стол "Ергономик"', 'Ергономичен офис стол с поддръжка на кръста, регулируема височина и подлакътници.', 'Столове', 349.99, 'chair1.jpg', 15),
('Библиотека "Класик"', 'Висока библиотека с 5 рафта. Изработена от МДФ с меламиново покритие в цвят венге.', 'Шкафове', 459.00, 'bookshelf1.jpg', 7),
('Холна секция "Модерна"', 'Модулна холна секция с място за телевизор, витрини и чекмеджета. Бял гланц.', 'Холни секции', 1799.99, 'living_room1.jpg', 4),
('Кухненски комплект "Практик"', 'Долни и горни шкафове за кухня 2.40м. Включва мивка и плот. Цвят: бял/червен.', 'Кухни', 2199.00, 'kitchen1.jpg', 2),
('Детско легло "Приказка"', 'Едноместно детско легло с защитна странична преграда. Размер 90x200см. Цветен дизайн.', 'Детски мебели', 549.99, 'kids_bed1.jpg', 6),
('Гардероб "Простор"', 'Голям гардероб с три врати, огледало и много място за съхранение. Размери: 200x220x60см.', 'Гардероби', 1299.00, 'wardrobe1.jpg', 5),
('Холна маса "Стил"', 'Стилна холна маса със стъклен плот и метална основа. Размер: 110x60см.', 'Маси', 299.99, 'coffee_table1.jpg', 10);
