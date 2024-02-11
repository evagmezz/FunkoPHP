DROP SEQUENCE IF EXISTS funkos_id_seq;
DROP TABLE IF EXISTS funkos;
DROP TABLE IF EXISTS categories;
DROP SEQUENCE IF EXISTS users_id_seq;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS users;

CREATE TABLE categories
(
    id         UUID PRIMARY KEY,
    name       VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    is_deleted BOOLEAN NOT NULL DEFAULT false
);

create sequence funkos_id_seq start 6;
CREATE TABLE funkos
(
    id          INT PRIMARY KEY,
    name        VARCHAR(255),
    image       VARCHAR(255),
    price       DECIMAL(10, 2),
    stock       INT,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    category_id UUID,
    is_deleted  BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY (category_id) REFERENCES categories (id)
);

insert into categories (id, name, created_at, updated_at, is_deleted)
values ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'MARVEL', now(), now(), false),
       ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a12', 'DISNEY', now(), now(), false),
       ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a13', 'DC', now(), now(), false);
INSERT INTO funkos (id, name, image, price, stock, category_id, created_at, updated_at, is_deleted)
VALUES (1, 'Funko Pop! Spiderman', 'http://localhost:8080/uploads/spiderman.jpg', 12.99, 100, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, false),
       (2, 'Funko Pop! Batman', 'http://localhost:8080/uploads/batman.png', 14.99, 50, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a13', CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, false),
       (3, 'Funko Pop! Iron Man', 'http://localhost:8080/uploads/ironmaan.png', 9.99, 75, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, false),
       (4, 'Funko Pop! Cruela', 'http://localhost:8080/uploads/funko1.jpg', 11.99, 80, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a12', CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, false),
       (5, 'Funko Pop! Merida', 'http://localhost:8080/uploads/merida.jpg', 8.99, 120, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a12', CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, false);

create sequence users_id_seq start 3;
CREATE TABLE users
(
    id         INT PRIMARY KEY,
    username   VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    name       VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL,
    updated_at TIMESTAMP
);

CREATE TABLE user_roles
(
    user_id INT,
    roles   CHARACTER(5)
);

INSERT INTO users (id, username, password, name, email, created_at, updated_at)
VALUES (1, 'admin', '$2a$12$W61HhKd1JQr0LQrfq2ZW7.0j8.CtbrK5YpRdbrZk0jHKB2PaD87wO', 'John',
        'john.doe@example.com', '2024-01-30 10:00:00', NULL),
       (2, 'user', '$2a$12$ir9T7EmLxpjHvUz9DynTm.5ESsyuDw6Ui/nOocxCgKbM4bsqCTVjC', 'Jane',
        'jane.smith@example.com', '2024-01-30 10:00:00', NULL);

INSERT INTO user_roles (user_id, roles)
VALUES (1, 'ADMIN'),
       (2, 'USER')