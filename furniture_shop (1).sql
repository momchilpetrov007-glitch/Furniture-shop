-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Време на генериране: 15 апр 2026 в 13:33
-- Версия на сървъра: 10.4.32-MariaDB
-- Версия на PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данни: `furniture_shop`
--

-- --------------------------------------------------------

--
-- Структура на таблица `custom_requests`
--

CREATE TABLE `custom_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `furniture_type` varchar(100) NOT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('pending','contacted','quoted','approved','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `custom_requests`
--

INSERT INTO `custom_requests` (`id`, `name`, `email`, `phone`, `furniture_type`, `dimensions`, `material`, `budget`, `description`, `status`, `created_at`) VALUES
(1, 'Момчил Петров', 'momchil.petrov007@gmail.com', '0886040722', 'Легло', '', '', '', 'иииии', 'pending', '2026-02-27 14:14:30'),
(2, 'Момчил Петров', 'momchil.petrov007@gmail.com', '0886040722', 'Маса', '', '', '', 'mmmmmm', 'quoted', '2026-03-03 07:54:07'),
(3, 'Момчил Петров', 'momchil.petrov007@gmail.com', '0886040722', 'Легло', '', '', '', 'kkk', 'pending', '2026-03-05 12:19:31'),
(4, 'Момчил Петров', 'momchil.petrov007@gmail.com', '0886040722', 'Маса', '', '', '', 'pppp', 'quoted', '2026-03-12 13:14:26');

-- --------------------------------------------------------

--
-- Структура на таблица `furniture`
--

CREATE TABLE `furniture` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `furniture`
--

INSERT INTO `furniture` (`id`, `name`, `description`, `category`, `price`, `image`, `stock`, `created_at`) VALUES
(2, 'Трапезна маса \"Елеганс\"', 'Красива дървена трапезна маса за 6 души. Изработена от масив дъб с естествен финиш.', 'Маси', 899.50, 'table1.jpg', 6, '2026-01-07 09:18:56'),
(3, 'Спалня \"Луксор\"', 'Комплект спалня включваща легло 160x200, 2 нощни шкафчета и гардероб. Модерен дизайн в тъмно кафяво.', 'Спални', 2499.00, 'bedroom1.jpg', 9, '2026-01-07 09:18:56'),
(4, 'Офис стол \"Ергономик\"', 'Ергономичен офис стол с поддръжка на кръста, регулируема височина и подлакътници.', 'Столове', 349.99, 'chair1.jpg', 11, '2026-01-07 09:18:56'),
(5, 'Библиотека \"Класик\"', 'Висока библиотека с 5 рафта. Изработена от МДФ с меламиново покритие в цвят венге.', 'Шкафове', 459.00, 'bookshelf1.jpg', 4, '2026-01-07 09:18:56'),
(6, 'Холна секция \"Модерна\"', 'Модулна холна секция с място за телевизор, витрини и чекмеджета. Бял гланц.', 'Холни секции', 1799.99, 'living_room1.jpg', 4, '2026-01-07 09:18:56'),
(7, 'Кухненски комплект \"Практик\"', 'Долни и горни шкафове за кухня 2.40м. Включва мивка и плот. Цвят: ', 'Кухни', 2199.00, 'kitchen1.jpg', 10, '2026-01-07 09:18:56'),
(8, 'Детско легло \"Приказка\"', 'Едноместно детско легло със защитна странична преграда. Размер 90x200см. Цветен дизайн.', 'Детски мебели', 549.99, 'kids_bed1.jpg', 5, '2026-01-07 09:18:56'),
(9, 'Гардероб \"Простор\"', 'Голям гардероб с три врати, огледало и много място за съхранение. Размери: 200x220x60см.', 'Гардероби', 1299.00, 'wardrobe1.jpg', 2, '2026-01-07 09:18:56'),
(10, 'Холна маса \"Стил\"', 'Стилна холна маса със стъклен плот и метална основа. Размер: 110x60см.', 'Маси', 299.99, 'coffee_table1.jpg', 7, '2026-01-07 09:18:56'),
(11, 'спален комлект', 'перфектен', 'спални комплекто', 769.00, '696f3f6263f2f_1768898402.jpg', 8, '2026-01-20 08:40:02');

-- --------------------------------------------------------

--
-- Структура на таблица `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `delivery_address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `delivery_address`, `phone`, `notes`, `created_at`) VALUES
(1, 3, 899.50, 'cancelled', 'tsar svetoslav terter 13', '0886040722', '', '2026-01-09 08:22:44'),
(2, 3, 2199.49, 'cancelled', 'tsar svetoslav terter 13', '0886040722', '', '2026-01-14 07:01:29'),
(4, 7, 899.50, '', 'tsar svetoslav terter 13', '0886040722', '', '2026-02-10 12:08:14'),
(5, 7, 769.00, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-02-10 12:23:46'),
(6, 7, 4398.00, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-02-12 14:35:28'),
(7, 7, 1799.00, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: card', '2026-02-16 14:29:30'),
(8, 7, 299.99, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-02-20 12:48:41'),
(9, 7, 599.98, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-02-20 12:53:31'),
(10, 7, 8396.50, '', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-02-20 14:29:42'),
(11, 7, 459.00, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: cash', '2026-02-24 12:49:11'),
(12, 7, 549.99, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todor@gmail.com\nНачин на плащане: cash', '2026-02-24 12:54:05'),
(13, 7, 349.99, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: cash', '2026-02-24 12:55:13'),
(14, 7, 459.00, 'pending', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-02-24 13:01:13'),
(15, 7, 1299.00, 'cancelled', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: cash', '2026-02-26 12:30:44'),
(16, 7, 459.00, 'shipped', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-03-02 15:15:29'),
(17, 7, 1299.00, 'delivered', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-03-03 07:39:59'),
(18, 7, 349.99, 'confirmed', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: cash', '2026-03-03 07:42:24'),
(19, 7, 349.99, 'cancelled', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-03-03 07:53:08'),
(20, 7, 349.99, 'cancelled', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: card', '2026-03-05 12:41:41'),
(21, 7, 1299.00, 'delivered', 'tsar svetoslav terter 13, varna, 9000', '0886040722', 'Име: Момчил Петров\nИмейл: todortodorov26@itpg-varna.bg\nНачин на плащане: card\nПлатено с карта: Успешно', '2026-03-12 13:17:01');

-- --------------------------------------------------------

--
-- Структура на таблица `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `furniture_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `furniture_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 899.50),
(3, 2, 2, 1, 899.50),
(5, 4, 2, 1, 899.50),
(6, 5, 11, 1, 769.00),
(7, 6, 7, 2, 2199.00),
(8, 7, 2, 2, 899.50),
(9, 8, 10, 1, 299.99),
(10, 9, 10, 2, 299.99),
(11, 10, 2, 1, 899.50),
(12, 10, 3, 3, 2499.00),
(13, 11, 5, 1, 459.00),
(14, 12, 8, 1, 549.99),
(15, 13, 4, 1, 349.99),
(16, 14, 5, 1, 459.00),
(17, 15, 9, 1, 1299.00),
(18, 16, 5, 1, 459.00),
(19, 17, 9, 1, 1299.00),
(20, 18, 4, 1, 349.99),
(21, 19, 4, 1, 349.99),
(22, 20, 4, 1, 349.99),
(23, 21, 9, 1, 1299.00);

-- --------------------------------------------------------

--
-- Структура на таблица `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `full_name`, `phone`, `address`, `role`, `created_at`) VALUES
(3, 'momos1607', 'momchil.petrov007@gmail.com', '$2y$10$hMyrlIId0UxhBYiTr5/81.QrOZBNQ/rxc40BcaWVuWgd.QuMJ1bK2', 1, 'Momchil', '0886040722', 'tsar svetoslav terter 13', 'admin', '2026-01-09 08:20:58'),
(5, 'administrator', 'administrator@furniture.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 'Главен Администратор', NULL, NULL, 'admin', '2026-01-09 08:35:55'),
(7, 'todor', 'todor@gmail.com', '$2y$10$NnFYuGSEIwJROocyJjlov.sV2DwiRxkWshwcR9Q8fckZ6MPHnJaeS', 0, 'todor', '0886040722', 'tsar svetoslav terter 13', 'user', '2026-02-03 12:32:36');

--
-- Indexes for dumped tables
--

--
-- Индекси за таблица `custom_requests`
--
ALTER TABLE `custom_requests`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `furniture`
--
ALTER TABLE `furniture`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индекси за таблица `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `furniture_id` (`furniture_id`);

--
-- Индекси за таблица `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `custom_requests`
--
ALTER TABLE `custom_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `furniture`
--
ALTER TABLE `furniture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения за дъмпнати таблици
--

--
-- Ограничения за таблица `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения за таблица `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`furniture_id`) REFERENCES `furniture` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
