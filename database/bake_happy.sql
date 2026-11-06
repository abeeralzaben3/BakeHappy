-- =====================================================
-- Bake Happy | Mini Project Database (MySQL / InnoDB)
-- 5 tables: users, categories, products, orders, order_items
-- =====================================================
DROP DATABASE IF EXISTS bake_happy;
CREATE DATABASE bake_happy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bake_happy;

-- ---------- USERS ----------
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,                 -- password_hash()
  phone         VARCHAR(20),
  role          ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- CATEGORIES ----------
CREATE TABLE categories (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  image       VARCHAR(255),                            -- uploads/categories/xxx.jpg
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- PRODUCTS ----------
CREATE TABLE products (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name        VARCHAR(150) NOT NULL,
  description TEXT,
  price       DECIMAL(8,2) NOT NULL,                   -- JD
  stock       INT UNSIGNED NOT NULL DEFAULT 0,
  image       VARCHAR(255),                            -- uploads/products/xxx.jpg
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
  INDEX idx_name (name)
) ENGINE=InnoDB;

-- ---------- ORDERS (COD) ----------
CREATE TABLE orders (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id        INT UNSIGNED NOT NULL,
  full_name      VARCHAR(100) NOT NULL,
  phone          VARCHAR(20)  NOT NULL,
  city           VARCHAR(80)  NOT NULL,
  address        VARCHAR(255) NOT NULL,
  notes          VARCHAR(255),
  total          DECIMAL(10,2) NOT NULL,
  payment_method ENUM('COD') NOT NULL DEFAULT 'COD',
  payment_status ENUM('Unpaid','Paid') NOT NULL DEFAULT 'Unpaid',
  status         ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------- ORDER ITEMS (snapshot of name/price at purchase time) ----------
CREATE TABLE order_items (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id     INT UNSIGNED NOT NULL,
  product_id   INT UNSIGNED NULL,
  product_name VARCHAR(150) NOT NULL,
  price        DECIMAL(8,2) NOT NULL,
  quantity     INT UNSIGNED NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- SEED DATA
-- =====================================================
-- Admin: register a normal account from the site, then run:
--   UPDATE users SET role='admin' WHERE email='your@email.com';

INSERT INTO categories (name, description) VALUES
('Baking Essentials',          'Chocolate, cocoa, vanilla, sprinkles, flours and baking mixes.'),
('Nuts & Dried Fruits',        'Pistachios, pecans, macadamia, almonds, cashews, dried berries and mango.'),
('Chocolate & Sweet Treats',   'Dark, milk and white chocolate, spreads, truffles and specialty sweets.'),
('Herbal Tea & Natural Goods', 'Chamomile, hibiscus, lavender, dried mint, cinnamon sticks and herbal blends.');

INSERT INTO products (category_id, name, description, price, stock) VALUES
(1, 'Couverture Chocolate 200g', 'Professional dark couverture for baking and tempering.', 4.50, 40),
(1, 'Vanilla Extract 100ml',     'Pure vanilla extract, hard to find locally.',            6.00, 25),
(1, 'Rainbow Sprinkles 100g',    'Colorful sprinkles for cakes and cookies.',              2.25, 60),
(2, 'Jordanian Pistachios 180g', 'Salted and roasted pistachios.',                         6.50, 40),
(2, 'Maple Glazed Pecans 170g',  'Crisp cinnamon-coated pecans.',                          5.75, 30),
(2, 'Dried Mango Slices 150g',   'Sweet sun-dried mango.',                                 3.50, 50),
(3, 'Ruby Chocolate Discs 175g', 'Naturally berry-fruity notes.',                          7.25, 30),
(3, 'Belgian Dark Callets 200g', 'Baking couverture callets.',                             5.00, 35),
(4, 'Organic Hibiscus 110g',     'Tart infusion and cake garnish.',                        3.00, 45),
(4, 'Cinnamon Sticks 80g',       'Whole Ceylon cinnamon sticks.',                          2.75, 55);