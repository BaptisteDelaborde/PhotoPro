DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS gallery_photos;
DROP TABLE IF EXISTS photos;
DROP TABLE IF EXISTS galleries;

CREATE TABLE galleries (
    id UUID PRIMARY KEY,
    photographer_id UUID NOT NULL,

    title VARCHAR(255) NOT NULL,
    description TEXT,

    cover_photo_id UUID,

    is_public BOOLEAN NOT NULL DEFAULT TRUE,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,

    layout VARCHAR(50) NOT NULL,

    access_code VARCHAR(50),
    access_url TEXT,

    client_name VARCHAR(255),
    client_email VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    published_at TIMESTAMP
);

CREATE TABLE photos (
    id UUID PRIMARY KEY,
    photographer_id UUID NOT NULL,

    title VARCHAR(255),
    file_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size FLOAT NOT NULL,

    storage_url TEXT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gallery_photos (
    gallery_id UUID NOT NULL,
    photo_id UUID NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (gallery_id, photo_id),
    CONSTRAINT fk_gp_gallery FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE,
    CONSTRAINT fk_gp_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id UUID PRIMARY KEY,
    photo_id UUID NOT NULL,

    author_name VARCHAR(255),
    content TEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_photo_comment FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE
);