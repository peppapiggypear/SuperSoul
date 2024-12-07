-- Set SQL mode and time zone
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS retail;
USE retail;

-- Table: admins
CREATE TABLE IF NOT EXISTS `admins` (
  `adminID` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`adminID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admins` (`adminID`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$PZtWdGIdxR5nNE4/Oy.gQujEOceQl7VOy4ScoYoq2oHEnFTx0lh8C');

-- Table: customers
CREATE TABLE IF NOT EXISTS `customers` (
  `customerID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`customerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`customerID`, `name`) VALUES
(15, 'Kylie'),
(16, 'Jennie'),
(17, 'Devin Booker'),
(18, 'Rihanna');

-- Table: products
CREATE TABLE IF NOT EXISTS `products` (
  `productID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock_level` int NOT NULL,
  `image_url` varchar(2083) NOT NULL,
  PRIMARY KEY (`productID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`productID`, `name`, `description`, `price`, `stock_level`, `image_url`) VALUES
(7, 'Oreo', 'Cookie n Cream', 2.00, 96, 'https://png.pngtree.com/png-vector/20240730/ourmid/pngtree-oreo-cookies-from-classic-to-modern-twists-png-image_13305063.png'),
(8, 'Lays', 'Cheddar Cheese Potato Chips', 3.00, 99, 'https://i.pinimg.com/736x/62/88/d0/6288d08f74f0826429bc77d875a9461e.jpg'),
(9, 'Takis', 'Hot & Lime Tortilla Chips', 5.00, 98, 'https://www.pngmart.com/files/23/Takis-PNG-Isolated-File.png'),
(10, 'Diet Pepsi', 'Zero-calories Cola Drink', 0.50, 100, 'https://nosherz.com/cdn/shop/products/Diet-Pepsi-v2_12oz_4472x.png?v=1643129805'),
(11, 'Chobani', 'Greek Yoghurt Coconut', 3.00, 99, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSUOJHNqrDdVaKqqKLyUTACiLKIxIjLBVK3ug&s'),
(12, 'Buldak Chicken Ramen', 'Korean Spicy Chicken Instant Noodles', 2.00, 9, 'https://www.salamat.gr/image/cache/catalog/chris/CUPBULLLLL-600x600.png');

-- Table: orders
CREATE TABLE IF NOT EXISTS `orders` (
  `orderID` int NOT NULL AUTO_INCREMENT,
  `customerID` int NOT NULL,
  `order_date` datetime NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `contact` varchar(100) NOT NULL,
  PRIMARY KEY (`orderID`),
  KEY `customerID` (`customerID`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`orderID`, `customerID`, `order_date`, `order_total`, `shipping_address`, `contact`) VALUES
(8, 15, '2024-11-24 10:09:13', 4.00, 'LA', ''),
(9, 17, '2024-11-27 15:35:34', 10.00, 'AZ', 'devin@hotmail.com'),
(10, 17, '2024-11-27 15:40:41', 3.00, 'LA', '099-999-9999'),
(11, 17, '2024-11-27 15:44:36', 4.00, 'Houston', '099-999-9999'),
(12, 18, '2024-11-27 15:46:26', 3.00, '444/44 Georgetown', 'fenty@beauty.com');

-- Table: payments
CREATE TABLE IF NOT EXISTS `payments` (
  `paymentID` int NOT NULL AUTO_INCREMENT,
  `orderID` int NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`paymentID`),
  KEY `orderID` (`orderID`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payments` (`paymentID`, `orderID`, `payment_method`, `payment_status`, `amount`) VALUES
(8, 8, 'Credit Card', 'Completed', 4.00),
(9, 9, 'Credit Card', 'Completed', 10.00),
(10, 10, 'Credit Card', 'Completed', 3.00),
(11, 11, 'PayPal', 'Completed', 4.00),
(12, 12, 'Credit Card', 'Completed', 3.00);

-- Table: orderitems
CREATE TABLE IF NOT EXISTS `orderitems` (
  `orderID` int NOT NULL,
  `productID` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`orderID`, `productID`),
  KEY `productID` (`productID`),
  CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`),
  CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orderitems` (`orderID`, `productID`, `quantity`, `unit_price`) VALUES
(8, 7, 2, 2.00),
(9, 9, 2, 5.00),
(10, 8, 1, 3.00),
(11, 7, 2, 2.00),
(12, 11, 1, 3.00);

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `password_hash` char(64) NOT NULL,
  `customerID` int NOT NULL,
  `username` varchar(255) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `customerID` (`customerID`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`userID`, `password_hash`, `customerID`, `username`, `name`) VALUES
(18, '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 15, 'kylie', 'Kylie'),
(19, 'a889fa49b78d9e524860f9ac82cac7c1440837b5e95d651602b26cba46d600bd', 16, 'jenn', 'Jennie'),
(20, '556d7dc3a115356350f1f9910b1af1ab0e312d4b3e4fc788d2da63668f36d017', 17, 'devin', 'Devin Booker'),
(21, '1f41052f77c1083c67250cebc1c37f0903e5c9706676e351d6be4482d9b2da17', 18, 'riri', 'Rihanna');

-- Table: shoppingcart
CREATE TABLE IF NOT EXISTS `shoppingcart` (
  `cartID` int NOT NULL AUTO_INCREMENT,
  `customerID` int NOT NULL,
  `productID` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`cartID`),
  KEY `customerID` (`customerID`),
  KEY `productID` (`productID`),
  CONSTRAINT `shoppingcart_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`),
  CONSTRAINT `shoppingcart_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- View: low_stock_products
DROP VIEW IF EXISTS `low_stock_products`;
CREATE VIEW `low_stock_products` AS
SELECT
    `productID`,
    `name` AS `product_name`,
    `stock_level`
FROM
    `products`
WHERE
    `stock_level` < 10;

-- View: product_sales
DROP VIEW IF EXISTS `product_sales`;
CREATE VIEW `product_sales` AS
SELECT
    p.`productID`,
    p.`name` AS `product_name`,
    SUM(oi.`quantity`) AS `total_quantity_sold`,
    SUM(oi.`quantity` * oi.`unit_price`) AS `total_sales`
FROM
    `products` p
INNER JOIN
    `orderitems` oi ON p.`productID` = oi.`productID`
GROUP BY
    p.`productID`, p.`name`;

CREATE USER 'dbadmin_user'@'localhost' IDENTIFIED BY 'dbadmin_password';
CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'admin_password';
CREATE USER 'customer_user'@'localhost' IDENTIFIED BY 'customer_password';

GRANT ALL PRIVILEGES ON `retail`.* TO 'dbadmin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON `retail`.* TO 'admin_user'@'localhost';
GRANT SELECT ON `retail`.* TO 'customer_user'@'localhost';

FLUSH PRIVILEGES;

-- Remove unnecessary transaction commands

