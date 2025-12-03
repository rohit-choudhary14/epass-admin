CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100)
);

INSERT INTO users (username, password, fullname)
VALUES ('admin', '$2y$10$kX3Jd2xgKjHf0qEI7c7/9eP9PzH6a7s1LqXg7R6GMLp3sMpLZBA9q', 'Administrator');
