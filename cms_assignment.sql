-- =========================================
-- DATABASE
-- =========================================

CREATE DATABASE IF NOT EXISTS cms_assignment;
USE cms_assignment;

-- =========================================
-- CLEAN TABLES
-- =========================================

DROP TABLE IF EXISTS post_categories;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

-- =========================================
-- USERS TABLE
-- =========================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- POSTS TABLE
-- =========================================

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft','published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user_post
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- CATEGORIES TABLE
-- =========================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- POST_CATEGORIES JUNCTION TABLE
-- =========================================

CREATE TABLE post_categories (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (post_id, category_id),
    CONSTRAINT fk_post_category
    FOREIGN KEY (post_id) REFERENCES posts(id)
    ON DELETE CASCADE,
    CONSTRAINT fk_category_post
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- INDEXES
-- =========================================

CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_post_user ON posts(user_id);
CREATE INDEX idx_post_date ON posts(created_at);
CREATE INDEX idx_post_status ON posts(status);
CREATE INDEX idx_category_slug ON categories(slug);
CREATE INDEX idx_post_cat_post ON post_categories(post_id);
CREATE INDEX idx_post_cat_category ON post_categories(category_id);

-- =========================================
-- SAMPLE CATEGORIES
-- =========================================

INSERT INTO categories (name, slug, description) VALUES
('Technology', 'technology', 'Posts about technology and software development'),
('Business', 'business', 'Business related articles'),
('Life', 'life', 'Personal life and lifestyle posts'),
('Travel', 'travel', 'Travel and adventure stories');


-- =========================================
-- SAMPLE POSTS
-- =========================================

INSERT INTO posts (user_id,title,content,status) VALUES
(2,'My First Post','This is the first post created by user.','published'),
(2,'Second Post','This is another example post for testing.','published'),
(1,'Admin Announcement','This post is created by admin.','published');

-- =========================================
-- SAMPLE POST_CATEGORIES
-- =========================================

INSERT INTO post_categories (post_id, category_id) VALUES
(1, 1), -- First post: Technology
(2, 2), -- Second post: Business
(3, 1); -- Admin announcement: Technology