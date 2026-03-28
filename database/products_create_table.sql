-- Products table (MySQL) — matches the Laravel store (name, brand, category, price, rating, image, stock, timestamps).
-- Run this once on an empty database, or only if the `products` table does not exist yet.

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `brand` VARCHAR(100) NOT NULL,
    `category` ENUM('phones', 'laptops') NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `rating` DECIMAL(2, 1) NOT NULL,
    `image` TEXT NOT NULL,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `products_brand_index` (`brand`),
    KEY `products_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
