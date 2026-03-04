
INSERT INTO products (name, brand, category, price, rating, image) VALUES
('iPhone 15', 'Apple', 'phones', 799, 4.8, 'Image/product/Iphone15.jpg');

-- Example: multiple products (match your files in public/Image/product/ or public/image/product/)
INSERT INTO products (name, brand, category, price, rating, image) VALUES
('iPhone 15 Pro Max', 'Apple', 'phones', 1199, 4.9, 'Image/product/Iphone15promax.jpg'),
('iPhone 15', 'Apple', 'phones', 799, 4.8, 'Image/product/Iphone15.jpg'),
('MacBook Pro 14"', 'Apple', 'laptops', 1999, 4.9, 'Image/product/macbookpro14.jpg'),
('iPad Pro 12.9"', 'Apple', 'laptops', 1099, 4.8, 'Image/product/ipadpro129.jpg'),
('ThinkPad X1 Carbon', 'Lenovo', 'laptops', 1499, 4.7, 'Image/product/Lenovo ThinkPad X1 Carbon.jpg'),
('IdeaPad Slim 5', 'Lenovo', 'laptops', 899, 4.6, 'Image/product/ideapad5.jpg'),
('Honor Magic 6 Pro', 'Honor', 'phones', 699, 4.7, 'Image/product/honor_magic6.jpg'),
('Honor 90', 'Honor', 'phones', 449, 4.5, 'Image/product/honor90.jpg');

-- Note: Use "Image/product/..." (capital I) or "image/product/..." (lowercase) to match
-- the real folder name under public/. Windows is case-insensitive; Linux is case-sensitive.
