-- Buat file ini berisi struktur tabel
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE category (
    id_category INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE recipe (
    id_recipe INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_category INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user),
    FOREIGN KEY (id_category) REFERENCES category(id_category)
);

CREATE TABLE recipe_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    ingredients TEXT, -- JSON format
    steps TEXT, -- JSON format  
    notes TEXT,
    FOREIGN KEY (recipe_id) REFERENCES recipe(id_recipe) ON DELETE CASCADE
);

CREATE TABLE comment (
    id_comment INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_recipe INT,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user),
    FOREIGN KEY (id_recipe) REFERENCES recipe(id_recipe) ON DELETE CASCADE
);

CREATE TABLE favorite (
    id_favorite INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_recipe INT,
    id_comment INT,
    FOREIGN KEY (id_user) REFERENCES user(id_user),
    FOREIGN KEY (id_recipe) REFERENCES recipe(id_recipe) ON DELETE CASCADE,
    FOREIGN KEY (id_comment) REFERENCES comment(id_comment) ON DELETE SET NULL
);