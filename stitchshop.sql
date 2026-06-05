-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 27 2026 г., 20:10
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `stitchshop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Мягкие игрушки', 'Плюшевые мишки, зайцы и другие мягкие игрушки'),
(2, 'Коллекционные фигурки', 'Коллекционные фигурки персонажей');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `email`, `address`, `total_amount`, `status`, `created_at`) VALUES
(1, 3, 'admin', 'admin@gmail.com', 'астрахань', 2000.00, 'cancelled', '2026-04-21 18:08:30');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 2500.00),
(2, 2, 1, 1, 2500.00),
(3, 2, 2, 1, 4500.00),
(4, 3, 2, 1, 2000.00),
(5, 4, 2, 1, 2000.00);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category_id`, `image`, `specifications`, `created_at`) VALUES
(1, 'miniso stitch', 'Цвет синий\r\nНаполнитель синтепон\r\nМатериал игрушки полиэстер\r\nВид упаковки пакет\r\nСтрана производства Китай', 1556.00, 1, '69e62bf8d48d3.png', '{\"height\": \"30 см\", \"material\": \"Пластиковый наполнитель\", \"brand\": \"Disney\"}', '2026-04-15 14:22:40'),
(2, 'Дьявольский Стич', 'Количество предметов в упаковке 1 шт.\r\nМатериал игрушки винил\r\nОригинальная лицензионная продукция; высокая детализация\r\nЭффекты интересная лицензионная игрушка; \r\nСтрана производства Вьетнам', 2000.00, 2, '69e62c5fbce18.png', '{\"height\": \"15 см\", \"material\": \"ПВХ\", \"scale\": \"1:6\"}', '2026-04-15 14:22:40'),
(3, 'Стич с уткой', 'Харктеристики:\r\nЦвет синий; белый.\r\nМатериалы Наполнитель\r\nхоллофайбер.\r\nСтрана производства\r\nКитай', 980.00, 1, '69e62ca8dc2cd.png', '{\"height\": \"25 см\", \"material\": \"Холлофайбер\", \"brand\": \"Disney\"}', '2026-04-15 14:22:40'),
(4, 'Мягкая игрушка Стич', 'Цвет синий\r\nМатериалы Наполнитель синтепон\r\nМатериал игрушки полиэстер\r\nВид упаковки зип пакет\r\nСтрана производства Китай', 900.00, 1, '69e62d19c3012.png', '{\"count\": \"5 шт\", \"material\": \"ПВХ\", \"height\": \"10-15 см\"}', '2026-04-15 14:22:40'),
(5, 'Мягкий Стич', 'Цвет синий\r\nНаполнитель синтепон\r\nМатериал игрушки полиэстер\r\nВид упаковки пакет\r\nСтрана производства Китай', 950.00, 1, '69e62dd3e272d.png', '{\"height\": \"60 см\", \"material\": \"Мягкий плюш\", \"brand\": \"Disney\"}', '2026-04-15 14:22:40'),
(6, 'Фигурка Стич и Красный', 'Количество предметов в упаковке 1 шт.\r\nМатериал игрушки винил\r\nОсобенности игрушки\r\nОригинальная лицензионная продукция; высокая детализация\r\nСтрана производства Вьетнам\r\nКомплектация\r\nФигурка Funko Bitty POP! Rides Disney Lilo and Stitch Stitch and The Red One 85522\r\nГабаритыВысота предмета 2 см', 3800.00, 2, '69e63018a18f9.png', '{\"height\": \"12 см\", \"material\": \"ПВХ\", \"scale\": \"1:6\"}', '2026-04-15 14:22:40'),
(7, 'Стиченок', 'Цвет синий\r\nНаполнитель синтепон\r\nМатериал игрушки полиэстер\r\nВид упаковки пакет\r\nСтрана производства Китай\r\nКомплектация\r\nМягкая игрушка - 1 шт.\r\nДлина упаковки 12 см\r\nВысота упаковки 5 см\r\nШирина упаковки 6 см\r\nВес с упаковкой (кг) 0.4 кг\r\nВысота предмета 22 см\r\nГлубина предмета 20 см\r\nШирина предмета 20 см', 550.00, 1, '69e6305563aec.png', '{\"height\": \"28 см\", \"material\": \"Холлофайбер\", \"brand\": \"Disney\"}', '2026-04-15 14:22:40'),
(8, 'Плюшевый Стич', 'Цвет синий\r\nНаполнитель холлофайбер\r\nМатериал игрушки плюш\r\nЭффекты подарок\r\nВид упаковки пакет\r\nСтрана производства Китай\r\nИгрушка - 1 шт\r\nВысота предмета 25 см\r\nДлина упаковки 20 см\r\nВысота упаковки 20 см\r\nШирина упаковки 10 см\r\nВес с упаковкой (кг) 0.2 кг', 680.00, 1, '69e630ddbf888.png', '{\"height\": \"14 см\", \"material\": \"ПВХ\", \"scale\": \"1:6\"}', '2026-04-15 14:22:40'),
(9, 'Фигурка Тыква Стич', 'Количество предметов в упаковке 1 шт.\r\nМатериал игрушки Винил\r\nОригинальная лицензионная продукция; высокая детализация; \r\nИнтересная лицензионная игрушка; \r\nСтрана производства Вьетнам\r\nКомплектация\r\nФигурка Funko POP! Disney Lilo and Stitch Pumpkin Stitch (BLKLT) (Exc) (1498) 81969\r\nГабаритыВысота предмета\r\n9.5 см', 2600.00, 2, '69e63126b340e.png', '{\"height\": \"32 см\", \"material\": \"Мягкий плюш\", \"brand\": \"Disney\"}', '2026-04-15 14:22:40'),
(10, 'Игрушка лабубу стич', 'Цвет голубой; розовый; желтый\r\nНаполнитель полиэстер\r\nМатериал игрушки полиэстер; пластик\r\nВид упаковки воздушная пузырчатая пленка\r\nСтрана производства Китай\r\nМягкая игрушка брелок - 1шт\r\nКоллекционная модель\r\nВысота предмета 15 см\r\nДлина упаковки 18 см\r\nВысота упаковки 5 см\r\nШирина упаковки 10 см\r\nВес с упаковкой (кг) 0.8 кг', 250.00, NULL, '69e631605fefc.png', '{\"height\": \"40 см\", \"material\": \"Каучук\", \"artist\": \"Unique\"}', '2026-04-15 14:22:40'),
(11, 'Стич игрушка мягкая голубая', 'Цвет синий\r\nНаполнитель холлофайбер\r\nМатериал игрушки флис\r\nЭффекты нет\r\nВид упаковки пакет zip lock\r\nСтрана производства Китай\r\nСтич игрушка мягкая 1шт\r\nГабаритыВысота предмета 20 см\r\nГлубина предмета 7 см\r\nШирина предмета 10 см\r\nВес товара с упаковкой (г) 100 г\r\nДлина упаковки 15 см\r\nВысота упаковки 5 см\r\nШирина упаковки 10 см', 600.00, 1, '69e631e0b289e.png', '{\"height\": \"20 см\", \"material\": \"Каучук\"}', '2026-04-20 14:02:08');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
(2, 'Иван', 'ivankotoc10@gmail.com', '$2y$10$HZYsDuTVSrYPA21sYIKrYeUVOvXxmPF4m8GDCP0bTiHwTr42DxHHK', 0, '2026-04-15 14:32:05'),
(3, 'admin', 'admin@gmail.com', '$2y$10$BlYO3oJI53x4N4IY1Y4PxekZ/CmH4Xd51qvwVvhvL7N9ix7eXzZPu', 1, '2026-04-15 14:37:08'),
(4, 'bibi', 'bibi@gmail.com', '$2y$10$IlZOGjVeUNMrmQYkpqeT2.2uQl.6Ms1gc6kRyHrlUe0pPkfDpt0Wa', 0, '2026-04-23 14:44:54');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
