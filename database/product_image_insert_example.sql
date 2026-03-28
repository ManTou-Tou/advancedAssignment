-- Example INSERTs for the `products` table.
--
-- id: Do NOT insert manually. MySQL assigns it automatically (AUTO_INCREMENT).
-- image: Path relative to the `public` folder, e.g. Image/product/Iphone15.jpg
-- stock: How many units are available to sell (decreases when customers complete checkout).

-- Multiple products (match your files in public/Image/product/ or public/image/product/)
INSERT INTO products (name, brand, category, price, rating, image, stock) VALUES
('iPhone 15 Pro Max', 'Apple', 'phones', 1199, 4.9, 'Image/product/Iphone15promax.jpg', 40),
('iPhone 15', 'Apple', 'phones', 799, 4.8, 'Image/product/Iphone15.jpg', 60),
('MacBook Pro 14"', 'Apple', 'laptops', 1999, 4.9, 'Image/product/macbookpro14.jpg', 25),
('iPad Pro 12.9"', 'Apple', 'laptops', 1099, 4.8, 'Image/product/ipadpro129.jpg', 30),
('ThinkPad X1 Carbon', 'Lenovo', 'laptops', 1499, 4.7, 'Image/product/Lenovo ThinkPad X1 Carbon.jpg', 20),
('IdeaPad Slim 5', 'Lenovo', 'laptops', 899, 4.6, 'Image/product/ideapad5.jpg', 35),
('Honor Magic 6 Pro', 'Honor', 'phones', 699, 4.7, 'Image/product/honor_magic6.jpg', 45),
('Honor 90', 'Honor', 'phones', 449, 4.5, 'Image/product/honor90.jpg', 55);

-- Note: Use "Image/product/..." (capital I) or "image/product/..." (lowercase) to match
-- the real folder name under public/. Windows is case-insensitive; Linux is case-sensitive.
