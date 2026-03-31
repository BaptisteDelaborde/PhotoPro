DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id UUID PRIMARY KEY,
    email VARCHAR(128) NOT NULL UNIQUE,
    password VARCHAR(256) NOT NULL,
    role SMALLINT NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS photographes;

CREATE TABLE photographes (
    id UUID PRIMARY KEY,
    auth_user_id UUID NOT NULL UNIQUE,
    pseudo VARCHAR(128) NOT NULL UNIQUE,
    first_name VARCHAR(128) NOT NULL,
    last_name VARCHAR(128) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    description TEXT,
    profile_image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);