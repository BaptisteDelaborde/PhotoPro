DROP TABLE IF EXISTS photographers;

CREATE TABLE photographers (
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