-- TechHouse — Music & Audio E-Commerce
-- MySQL / MariaDB schema + seed data
-- Import via phpMyAdmin or:  mysql -u root < techhouse.sql

DROP DATABASE IF EXISTS techhouse;
CREATE DATABASE techhouse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techhouse;

-- ---------- USERS ----------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) DEFAULT NULL,
    role            ENUM('admin','client') NOT NULL DEFAULT 'client',
    address         VARCHAR(255) DEFAULT NULL,
    city            VARCHAR(100) DEFAULT NULL,
    country         VARCHAR(80)  DEFAULT NULL,
    provider        ENUM('local','google','facebook') NOT NULL DEFAULT 'local',
    provider_id     VARCHAR(80)  DEFAULT NULL,
    avatar_url      VARCHAR(255) DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    UNIQUE KEY uniq_provider (provider, provider_id)
) ENGINE=InnoDB;

-- ---------- CATEGORIES (music-store rubrics) ----------
CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(60) NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    icon        VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB;

INSERT INTO categories (slug, name, description, icon) VALUES
  ('instruments',  'Instruments',   'Guitars, keyboards, drums and more', 'guitar'),
  ('dj-equipment', 'DJ Equipment',  'Controllers, mixers, turntables',    'disc'),
  ('studio-gear',  'Studio Gear',   'Monitors, interfaces, microphones',  'mic'),
  ('vinyl',        'Vinyl Records', 'New releases & rare pressings',      'vinyl');

-- ---------- PRODUCTS ----------
CREATE TABLE products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED NOT NULL,
    name            VARCHAR(200) NOT NULL,
    slug            VARCHAR(220) NOT NULL UNIQUE,
    brand           VARCHAR(80)  DEFAULT NULL,
    color           VARCHAR(40)  DEFAULT NULL,
    description     TEXT,
    price           DECIMAL(10,2) NOT NULL,
    stock           INT NOT NULL DEFAULT 0,
    image           VARCHAR(255) DEFAULT NULL,
    is_new          TINYINT(1) NOT NULL DEFAULT 0,
    ai_tags         VARCHAR(255) DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_brand (brand),
    INDEX idx_color (color),
    INDEX idx_price (price),
    INDEX idx_new   (is_new),
    FULLTEXT KEY ft_search (name, brand, description)
) ENGINE=InnoDB;

-- ---------- ORDERS ----------
CREATE TABLE orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    status          ENUM('pending','paid','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    total           DECIMAL(10,2) NOT NULL,
    shipping_addr   VARCHAR(255) NOT NULL,
    tracking_code   VARCHAR(40)  DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    name        VARCHAR(200) NOT NULL,
    unit_price  DECIMAL(10,2) NOT NULL,
    quantity    INT NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------- ANALYTICS ----------
CREATE TABLE analytics_visits (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED DEFAULT NULL,
    ip           VARCHAR(45) NOT NULL,
    country      VARCHAR(80) DEFAULT NULL,
    city         VARCHAR(100) DEFAULT NULL,
    page         VARCHAR(255) NOT NULL,
    user_agent   VARCHAR(255) DEFAULT NULL,
    visited_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page (page),
    INDEX idx_country (country),
    INDEX idx_date (visited_at)
) ENGINE=InnoDB;

CREATE TABLE analytics_product_views (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED DEFAULT NULL,
    viewed_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- ---------- SEED USERS ----------
-- Admin password: admin123  | Client password: client123
INSERT INTO users (name, email, password_hash, role) VALUES
  ('Admin',  'admin@techhouse.local',  '$2y$10$Y8KJX9.b9oH/oM3qV0z9JOq7vIZ8H3jD3UvWZmO/ZkqJv4f1mEYxC', 'admin'),
  ('Client', 'client@techhouse.local', '$2y$10$xQYZ2J4z9d3wjN6r0Y3eMOuP6sQbV7c8YqMy0nKx/T5RJ8t1wXjL.', 'client');
-- NOTE: hashes above are placeholders. On first run, reset them via:
--   UPDATE users SET password_hash = (PHP password_hash output) WHERE email=...;
-- Or use the included script: php tools/seed_passwords.php

-- ---------- SEED PRODUCTS ----------
INSERT INTO products (category_id, name, slug, brand, color, description, price, stock, image, is_new) VALUES
  (1, 'Fender Player Stratocaster',     'fender-player-stratocaster', 'Fender',   'Sunburst', 'Iconic electric guitar, 22 frets, alder body.', 749.00, 12, 'strat.jpg', 1),
  (1, 'Yamaha P-125 Digital Piano',     'yamaha-p125',                'Yamaha',   'Black',    '88 weighted keys, GHS action.',                  649.00, 8,  'p125.jpg',  0),
  (1, 'Pearl Export 5-Piece Drum Kit',  'pearl-export-5pc',           'Pearl',    'Red',      'Complete 5-piece kit with hardware.',           899.00, 4,  'pearl.jpg', 0),
  (2, 'Pioneer DDJ-FLX4 Controller',    'pioneer-ddj-flx4',           'Pioneer',  'Black',    '2-channel DJ controller for Rekordbox/Serato.',  299.00, 15, 'flx4.jpg',  1),
  (2, 'Technics SL-1210MK7 Turntable',  'technics-sl1210mk7',         'Technics', 'Black',    'Direct-drive professional turntable.',          999.00, 6,  'sl1210.jpg',0),
  (2, 'Pioneer DJM-450 Mixer',          'pioneer-djm-450',            'Pioneer',  'Black',    '2-channel mixer with onboard FX.',              749.00, 5,  'djm450.jpg',0),
  (3, 'Yamaha HS8 Studio Monitors',     'yamaha-hs8-pair',            'Yamaha',   'White',    'Pair of nearfield monitors.',                   699.00, 10, 'hs8.jpg',   0),
  (3, 'Focusrite Scarlett 2i2 Gen 4',   'focusrite-2i2-g4',           'Focusrite','Red',      '2-in 2-out USB audio interface.',               199.00, 25, '2i2.jpg',   1),
  (3, 'Shure SM7B Microphone',          'shure-sm7b',                 'Shure',    'Black',    'Dynamic broadcast microphone.',                 419.00, 9,  'sm7b.jpg',  0),
  (4, 'Daft Punk — Random Access Memories (180g)', 'rams-vinyl',      'Columbia', 'Black',    'Double LP, 180g pressing.',                      45.00, 30, 'ram.jpg',   0),
  (4, 'Pink Floyd — The Dark Side of the Moon',    'dsotm-vinyl',     'EMI',      'Black',    'Remastered 180g vinyl.',                         32.00, 22, 'dsotm.jpg', 0),
  (4, 'Kendrick Lamar — DAMN. (Red Vinyl)',        'damn-vinyl',      'TDE',      'Red',      'Limited red pressing.',                          38.00, 14, 'damn.jpg',  1);
