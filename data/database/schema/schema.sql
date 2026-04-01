CREATE DATABASE IF NOT EXISTS taskforce
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE taskforce;

CREATE TABLE IF NOT EXISTS categories (
  id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  icon VARCHAR(50) NOT NULL,
  UNIQUE INDEX idx_name (name)
);

CREATE TABLE IF NOT EXISTS cities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  location POINT NOT NULL,
  UNIQUE INDEX idx_city_name (name),
  SPATIAL INDEX idx_location (location)
);


CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NULL,
  role ENUM('author', 'executor') NOT NULL,
  birthday DATE NULL,
  phone VARCHAR(11) NULL,
  tg VARCHAR(64) NULL,
  image_url VARCHAR(255) NULL,
  city_id INT UNSIGNED NULL,
  is_customer_only BOOLEAN NOT NULL DEFAULT FALSE,

  UNIQUE INDEX idx_email (email),
  INDEX idx_role (role),
  INDEX idx_city_id (city_id),

  FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE IF NOT EXISTS tasks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  category_id SMALLINT UNSIGNED NOT NULL,
  city_id INT UNSIGNED NULL,
  location_name VARCHAR(255) NULL,
  location POINT NOT NULL,
  budget INT UNSIGNED NULL,
  due_date DATE NULL,
  status ENUM('new', 'active', 'cancelled', 'completed', 'failed') NOT NULL DEFAULT 'new',
  author_id INT UNSIGNED NOT NULL,
  executor_id INT UNSIGNED NULL,

  INDEX idx_status (status),
  INDEX idx_category (category_id),
  INDEX idx_city (city_id),
  INDEX idx_author (author_id),
  INDEX idx_executor (executor_id),
  SPATIAL INDEX idx_location (location),

  FOREIGN KEY (author_id) REFERENCES users(id),
  FOREIGN KEY (executor_id) REFERENCES users(id),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE IF NOT EXISTS responses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  task_id INT UNSIGNED NOT NULL,
  executor_id INT UNSIGNED NOT NULL,
  price INT UNSIGNED NULL,
  comment TEXT NULL,
  status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',

  INDEX idx_task_id (task_id),
  INDEX idx_executor_id (executor_id),

  FOREIGN KEY (task_id) REFERENCES tasks(id),
  FOREIGN KEY (executor_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  task_id INT UNSIGNED NOT NULL,
  author_id INT UNSIGNED NOT NULL,
  executor_id INT UNSIGNED NOT NULL,
  comment TEXT NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,

  INDEX idx_executor (executor_id),
  INDEX idx_task (task_id),

  FOREIGN KEY (task_id) REFERENCES tasks(id),
  FOREIGN KEY (author_id) REFERENCES users(id),
  FOREIGN KEY (executor_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS task_files (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  task_id INT UNSIGNED NOT NULL,
  file_path VARCHAR(500) NOT NULL,

  FOREIGN KEY (task_id) REFERENCES tasks(id)
);

CREATE TABLE IF NOT EXISTS user_categories (
  user_id INT UNSIGNED NOT NULL,
  category_id SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, category_id),

  INDEX idx_user_id (user_id),
  INDEX idx_category_id (category_id),

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
  );
