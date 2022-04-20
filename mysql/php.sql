use bank;

DROP TABLE IF EXISTS banks;

CREATE TABLE banks (
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS product_types;

CREATE TABLE product_types (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id int NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS products;

CREATE TABLE products(
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    balance INT NOT NULL,
    bank_id INT NOT NULL,
    product_type_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (bank_id) REFERENCES banks(id),
    FOREIGN KEY (product_type_id) REFERENCES product_types(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS tef;

CREATE TABLE tef(
    id INT NOT NULL AUTO_INCREMENT,
    origin INT NOT NULL,
    destination INT NOT NULL,
    amount INT NOT NULL,
    message VARCHAR(255) DEFAULT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (origin) REFERENCES products(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS books;

CREATE TABLE books (
  id INT NOT NULL AUTO_INCREMENT,
  owner INT NOT NULL,
  fullname VARCHAR(100) NOT NULL,
  bank_id INT NOT NULL,
  product_type_id INT NOT NULL,
  product_number INT NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (owner) REFERENCES users(id),
  FOREIGN KEY (bank_id) REFERENCES banks(id),
  FOREIGN KEY (product_type_id) REFERENCES product_types(id)
) ENGINE=InnoDB;

INSERT INTO banks(id, name) VALUES(1, 'Bank PHP'),(2, 'Bank Pug'),(3, 'Bank ITSecurityCO');
INSERT INTO product_types(id, name) VALUES(1, 'Checking Account'),(2, 'Saving Account');
INSERT INTO users(id, username, password, fullname) VALUES(1, 'snowbell', '$2y$10$BxGFqG6Jc27rwxe3irYgLONJiSw09N0uGi430vnrSwegje6IfH9EK', 'Snowbell');
INSERT INTO products(id, user_id, bank_id, product_type_id, balance) VALUES(1337, 1, 1, 1, 1000000);